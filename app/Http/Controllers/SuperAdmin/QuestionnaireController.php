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
    public function index()
    {
        abort_if(Gate::denies('questionnaire_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaires = Questionnaire::with('category.treatment')
            ->withCount('sections')
            ->orderBy('id', 'DESC')
            ->get();

        return view('superAdmin.questionnaire.index', compact('questionnaires'));
    }

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

    public function store(Request $request)
    {
        abort_if(Gate::denies('questionnaire_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'category_id'                                    => 'required|exists:category,id|unique:questionnaires,category_id',
            'name'                                           => 'required|string|max:255',
            'description'                                    => 'nullable|string',
            'sections'                                       => 'required|array|min:1',
            'sections.*.name'                                => 'required|string|max:255',
            'sections.*.questions'                           => 'required|array|min:1',
            'sections.*.questions.*.question_text'           => 'required|string',
            'sections.*.questions.*.field_type'              => 'required|in:text,textarea,number,dropdown,radio,checkbox,file',
        ]);

        DB::beginTransaction();

        try {
            $questionnaire = Questionnaire::create([
                'category_id' => $request->category_id,
                'name'        => $request->name,
                'description' => $request->description,
                'status'      => $request->has('status') ? 1 : 0,
                'version'     => 1,
            ]);

            foreach ($request->sections as $sectionIndex => $sectionData) {
                $section = QuestionnaireSection::create([
                    'questionnaire_id' => $questionnaire->id,
                    'name'             => $sectionData['name'],
                    'description'      => $sectionData['description'] ?? null,
                    'order'            => $sectionIndex,
                ]);

                foreach ($sectionData['questions'] as $questionIndex => $questionData) {
                    QuestionnaireQuestion::create([
                        'section_id'       => $section->id,
                        'question_text'    => $questionData['question_text'],
                        'field_type'       => $questionData['field_type'],
                        'options'          => $this->parseOptions($questionData['options'] ?? null),
                        'required'         => isset($questionData['required']) ? 1 : 0,
                        'validation_rules' => $this->parseValidationRules($questionData),
                        'conditional_logic'=> null,
                        'flagging_rules'   => null,
                        'option_behaviors' => $this->parseOptionBehaviors($questionData['option_behaviors_json'] ?? null),
                        'doctor_notes'     => $questionData['doctor_notes'] ?? null,
                        'order'            => $questionIndex,
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

    public function show($id)
    {
        abort_if(Gate::denies('questionnaire_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaire = Questionnaire::with(['category.treatment', 'sections.questions'])
            ->findOrFail($id);

        return view('superAdmin.questionnaire.show', compact('questionnaire'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('questionnaire_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaire = Questionnaire::with(['category.treatment', 'sections.questions'])
            ->findOrFail($id);

        $categories = Category::whereStatus(1)->with('treatment')->orderBy('id', 'DESC')->get();
        $fieldTypes = QuestionnaireQuestion::FIELD_TYPES;

        return view('superAdmin.questionnaire.edit', compact('questionnaire', 'categories', 'fieldTypes'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('questionnaire_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaire = Questionnaire::findOrFail($id);

        $request->validate([
            'name'                                           => 'required|string|max:255',
            'description'                                    => 'nullable|string',
            'sections'                                       => 'required|array|min:1',
            'sections.*.name'                                => 'required|string|max:255',
            'sections.*.questions'                           => 'required|array|min:1',
            'sections.*.questions.*.question_text'           => 'required|string',
            'sections.*.questions.*.field_type'              => 'required|in:text,textarea,number,dropdown,radio,checkbox,file',
        ]);

        DB::beginTransaction();

        try {
            $questionnaire->update([
                'name'        => $request->name,
                'description' => $request->description,
                'status'      => $request->has('status') ? 1 : 0,
                'version'     => $questionnaire->version + 1,
            ]);

            $questionnaire->sections()->delete();

            foreach ($request->sections as $sectionIndex => $sectionData) {
                $section = QuestionnaireSection::create([
                    'questionnaire_id' => $questionnaire->id,
                    'name'             => $sectionData['name'],
                    'description'      => $sectionData['description'] ?? null,
                    'order'            => $sectionIndex,
                ]);

                foreach ($sectionData['questions'] as $questionIndex => $questionData) {
                    QuestionnaireQuestion::create([
                        'section_id'       => $section->id,
                        'question_text'    => $questionData['question_text'],
                        'field_type'       => $questionData['field_type'],
                        'options'          => $this->parseOptions($questionData['options'] ?? null),
                        'required'         => isset($questionData['required']) ? 1 : 0,
                        'validation_rules' => $this->parseValidationRules($questionData),
                        'conditional_logic'=> null,
                        'flagging_rules'   => null,
                        'option_behaviors' => $this->parseOptionBehaviors($questionData['option_behaviors_json'] ?? null),
                        'doctor_notes'     => $questionData['doctor_notes'] ?? null,
                        'order'            => $questionIndex,
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

    public function destroy($id)
    {
        abort_if(Gate::denies('questionnaire_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $questionnaire = Questionnaire::findOrFail($id);

        if ($questionnaire->appointments()->exists()) {
            return response(['success' => false, 'msg' => 'Cannot delete questionnaire that has been used in appointments!']);
        }

        $questionnaire->delete();

        return response(['success' => true]);
    }

    public function changeStatus(Request $request)
    {
        $questionnaire = Questionnaire::findOrFail($request->id);
        $questionnaire->update(['status' => $questionnaire->status ? 0 : 1]);

        return response(['success' => true]);
    }

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

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function parseOptions($options)
    {
        if (empty($options)) {
            return null;
        }

        if (is_array($options)) {
            return array_values(array_filter($options));
        }

        $parsed = preg_split('/[\n,]+/', $options);
        return array_values(array_filter(array_map('trim', $parsed)));
    }

    private function parseValidationRules($questionData)
    {
        $rules = [];

        if (!empty($questionData['min_length']))   $rules['min']           = (int) $questionData['min_length'];
        if (!empty($questionData['max_length']))   $rules['max']           = (int) $questionData['max_length'];
        if (!empty($questionData['regex_pattern'])) $rules['regex']        = $questionData['regex_pattern'];
        if (!empty($questionData['file_types']))   $rules['file_types']    = $this->parseOptions($questionData['file_types']);
        if (!empty($questionData['file_max_size'])) $rules['file_max_size'] = (int) $questionData['file_max_size'];

        return empty($rules) ? null : $rules;
    }

    /**
     * Parse and sanitize the option_behaviors JSON submitted by the builder.
     * The builder serialises the entire behaviors tree as a JSON string into
     * a hidden input. We decode, validate and clean it here.
     */
    private function parseOptionBehaviors(?string $json): ?array
    {
        if (empty($json)) {
            return null;
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded) || empty($decoded['behaviors'])) {
            return null;
        }

        $cleanedBehaviors = $this->sanitizeBehaviors($decoded['behaviors'], 1);

        return empty($cleanedBehaviors) ? null : ['behaviors' => $cleanedBehaviors];
    }

    /**
     * Recursively sanitize a behaviors array (max 3 levels deep).
     */
    private function sanitizeBehaviors(array $behaviors, int $depth): array
    {
        if ($depth > 3) {
            return [];
        }

        $validOperators = ['equals', 'not_equals', 'contains', 'greater_than', 'less_than', 'in'];
        $validTypes     = array_keys(QuestionnaireQuestion::FIELD_TYPES);
        $result         = [];

        foreach ($behaviors as $behavior) {
            if (!is_array($behavior)) {
                continue;
            }

            $condition = $behavior['condition'] ?? [];
            $operator  = in_array($condition['operator'] ?? '', $validOperators) ? $condition['operator'] : 'equals';
            $value     = $condition['value'] ?? '';

            // Sanitize flags
            $flags = [];
            foreach ($behavior['flags'] ?? [] as $flag) {
                if (!is_array($flag)) continue;
                $flagType = in_array($flag['flag_type'] ?? '', ['soft', 'hard']) ? $flag['flag_type'] : 'soft';
                $flags[]  = [
                    'flag_type'    => $flagType,
                    'flag_message' => trim($flag['flag_message'] ?? 'Answer flagged for review'),
                ];
            }

            // Sanitize sub_question
            $subQuestion = null;
            if (!empty($behavior['sub_question']) && is_array($behavior['sub_question']) && $depth < 3) {
                $sq = $behavior['sub_question'];
                $sqType = in_array($sq['field_type'] ?? '', $validTypes) ? $sq['field_type'] : 'text';

                $sqOptions = null;
                if (in_array($sqType, ['dropdown', 'radio', 'checkbox']) && !empty($sq['options'])) {
                    $sqOptions = array_values(array_filter(array_map('trim', (array) $sq['options'])));
                }

                $subQuestion = [
                    'temp_id'    => preg_replace('/[^a-zA-Z0-9_-]/', '', $sq['temp_id'] ?? ('sq_' . uniqid())),
                    'label'      => trim($sq['label'] ?? ''),
                    'field_type' => $sqType,
                    'options'    => $sqOptions,
                    'required'   => (bool) ($sq['required'] ?? false),
                    'placeholder'=> trim($sq['placeholder'] ?? ''),
                    'behaviors'  => $this->sanitizeBehaviors($sq['behaviors'] ?? [], $depth + 1),
                ];
            }

            $result[] = [
                'condition'    => ['operator' => $operator, 'value' => $value],
                'flags'        => $flags,
                'sub_question' => $subQuestion,
            ];
        }

        return $result;
    }
}
