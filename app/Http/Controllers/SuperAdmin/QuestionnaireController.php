<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireSection;
use App\Models\Category;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class QuestionnaireController extends Controller
{
    /**
     * Display a listing of all questionnaires.
     */
    public function index()
    {
        abort_if(Gate::denies('questionnaire_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaires = Questionnaire::with('category.treatment')
            ->withCount('sections')
            ->orderBy('id', 'DESC')
            ->get();

        return view('superAdmin.questionnaire.index', compact('questionnaires'));
    }

    /**
     * Show the form for creating a new questionnaire.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('questionnaire_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = Category::whereStatus(1)
            ->whereDoesntHave('questionnaire')
            ->with('treatment')
            ->orderBy('id', 'DESC')
            ->get();

        $selectedCategory = null;
        if ($request->has('category_id')) {
            $selectedCategory = Category::with('treatment')->find($request->category_id);
        }

        $fieldTypes = QuestionnaireQuestion::FIELD_TYPES;

        return view('superAdmin.questionnaire.create', compact('categories', 'selectedCategory', 'fieldTypes'));
    }

    /**
     * Store a newly created questionnaire.
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('questionnaire_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'category_id' => 'required|exists:category,id|unique:questionnaires,category_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sections' => 'required|array|min:1',
            'sections.*.name' => 'required|string|max:255',
            'sections.*.questions' => 'required|array|min:1',
            'sections.*.questions.*.question_text' => 'required|string',
            'sections.*.questions.*.field_type' => 'required|in:text,textarea,number,dropdown,radio,checkbox,file',
        ]);

        DB::beginTransaction();

        try {
            // Create questionnaire
            $questionnaire = Questionnaire::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->has('status') ? 1 : 0,
                'version' => 1,
            ]);

            // Create sections and questions
            foreach ($request->sections as $sectionIndex => $sectionData) {
                $section = QuestionnaireSection::create([
                    'questionnaire_id' => $questionnaire->id,
                    'name' => $sectionData['name'],
                    'description' => $sectionData['description'] ?? null,
                    'order' => $sectionIndex,
                ]);

                foreach ($sectionData['questions'] as $questionIndex => $questionData) {
                    QuestionnaireQuestion::create([
                        'section_id' => $section->id,
                        'question_text' => $questionData['question_text'],
                        'field_type' => $questionData['field_type'],
                        'options' => $this->parseOptions($questionData['options'] ?? null),
                        'required' => isset($questionData['required']) ? 1 : 0,
                        'validation_rules' => $this->parseValidationRules($questionData),
                        'conditional_logic' => $this->parseConditionalLogic($questionData['conditional_logic'] ?? null),
                        'flagging_rules' => $this->parseFlaggingRules($questionData),
                        'doctor_notes' => $questionData['doctor_notes'] ?? null,
                        'order' => $questionIndex,
                    ]);
                }
            }

            DB::commit();

            return redirect('questionnaire')->withStatus(__('Questionnaire created successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to create questionnaire: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified questionnaire.
     */
    public function show($id)
    {
        abort_if(Gate::denies('questionnaire_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaire = Questionnaire::with(['category.treatment', 'sections.questions'])
            ->findOrFail($id);

        return view('superAdmin.questionnaire.show', compact('questionnaire'));
    }

    /**
     * Show the form for editing the specified questionnaire.
     */
    public function edit($id)
    {
        abort_if(Gate::denies('questionnaire_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaire = Questionnaire::with(['category.treatment', 'sections.questions'])
            ->findOrFail($id);

        $categories = Category::whereStatus(1)->with('treatment')->orderBy('id', 'DESC')->get();
        $fieldTypes = QuestionnaireQuestion::FIELD_TYPES;

        return view('superAdmin.questionnaire.edit', compact('questionnaire', 'categories', 'fieldTypes'));
    }

    /**
     * Update the specified questionnaire.
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('questionnaire_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaire = Questionnaire::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sections' => 'required|array|min:1',
            'sections.*.name' => 'required|string|max:255',
            'sections.*.questions' => 'required|array|min:1',
            'sections.*.questions.*.question_text' => 'required|string',
            'sections.*.questions.*.field_type' => 'required|in:text,textarea,number,dropdown,radio,checkbox,file',
        ]);

        DB::beginTransaction();

        try {
            // Update questionnaire and increment version
            $questionnaire->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->has('status') ? 1 : 0,
                'version' => $questionnaire->version + 1,
            ]);

            // Delete existing sections and questions (cascade)
            $questionnaire->sections()->delete();

            // Recreate sections and questions
            foreach ($request->sections as $sectionIndex => $sectionData) {
                $section = QuestionnaireSection::create([
                    'questionnaire_id' => $questionnaire->id,
                    'name' => $sectionData['name'],
                    'description' => $sectionData['description'] ?? null,
                    'order' => $sectionIndex,
                ]);

                foreach ($sectionData['questions'] as $questionIndex => $questionData) {
                    QuestionnaireQuestion::create([
                        'section_id' => $section->id,
                        'question_text' => $questionData['question_text'],
                        'field_type' => $questionData['field_type'],
                        'options' => $this->parseOptions($questionData['options'] ?? null),
                        'required' => isset($questionData['required']) ? 1 : 0,
                        'validation_rules' => $this->parseValidationRules($questionData),
                        'conditional_logic' => $this->parseConditionalLogic($questionData['conditional_logic'] ?? null),
                        'flagging_rules' => $this->parseFlaggingRules($questionData),
                        'doctor_notes' => $questionData['doctor_notes'] ?? null,
                        'order' => $questionIndex,
                    ]);
                }
            }

            DB::commit();

            return redirect('questionnaire')->withStatus(__('Questionnaire updated successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to update questionnaire: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified questionnaire.
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('questionnaire_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaire = Questionnaire::findOrFail($id);

        // Check if questionnaire has any appointments
        if ($questionnaire->appointments()->exists()) {
            return response([
                'success' => false,
                'msg' => 'Cannot delete questionnaire that has been used in appointments!'
            ]);
        }

        $questionnaire->delete();

        return response(['success' => true]);
    }

    /**
     * Toggle questionnaire status.
     */
    public function changeStatus(Request $request)
    {
        $questionnaire = Questionnaire::findOrFail($request->id);
        $questionnaire->update(['status' => $questionnaire->status ? 0 : 1]);

        return response(['success' => true]);
    }

    /**
     * Get questionnaire for a category (API).
     */
    public function getForCategory($categoryId)
    {
        $questionnaire = Questionnaire::where('category_id', $categoryId)
            ->where('status', 1)
            ->with(['sections' => function ($query) {
                $query->orderBy('order')->with(['questions' => function ($q) {
                    $q->orderBy('order');
                }]);
            }])
            ->first();

        if (!$questionnaire) {
            return response()->json(['success' => false, 'data' => null]);
        }

        return response()->json(['success' => true, 'data' => $questionnaire]);
    }

    /**
     * Parse options string to array.
     */
    private function parseOptions($options)
    {
        if (empty($options)) {
            return null;
        }

        if (is_array($options)) {
            return array_filter($options);
        }

        // If string, split by newline or comma
        $parsed = preg_split('/[\n,]+/', $options);
        return array_filter(array_map('trim', $parsed));
    }

    /**
     * Parse validation rules from request data.
     */
    private function parseValidationRules($questionData)
    {
        $rules = [];

        if (!empty($questionData['min_length'])) {
            $rules['min'] = (int) $questionData['min_length'];
        }
        if (!empty($questionData['max_length'])) {
            $rules['max'] = (int) $questionData['max_length'];
        }
        if (!empty($questionData['regex_pattern'])) {
            $rules['regex'] = $questionData['regex_pattern'];
        }
        if (!empty($questionData['file_types'])) {
            $rules['file_types'] = $this->parseOptions($questionData['file_types']);
        }
        if (!empty($questionData['file_max_size'])) {
            $rules['file_max_size'] = (int) $questionData['file_max_size'];
        }

        return empty($rules) ? null : $rules;
    }

    /**
     * Parse conditional logic from request data.
     */
    private function parseConditionalLogic($conditionalLogic)
    {
        if (empty($conditionalLogic)) {
            return null;
        }

        if (is_string($conditionalLogic)) {
            $decoded = json_decode($conditionalLogic, true);
            return $decoded ?: null;
        }

        return $conditionalLogic;
    }

    /**
     * Parse flagging rules from request data.
     */
    private function parseFlaggingRules($questionData)
    {
        if (empty($questionData['flag_type']) || empty($questionData['flag_value'])) {
            return null;
        }

        return [
            'flag_type' => $questionData['flag_type'],
            'conditions' => [
                [
                    'operator' => $questionData['flag_operator'] ?? 'equals',
                    'value' => $questionData['flag_value'],
                    'flag_message' => $questionData['flag_message'] ?? 'Answer flagged for review',
                ]
            ]
        ];
    }
}



