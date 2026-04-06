{{--
  Renders one block inside an article for the edit view (pre-populated).
  Variables: $block (array), $ai (article index), $bi (block index)
--}}
@php $type = $block['type'] ?? 'text'; @endphp

@if($type === 'text')
<div class="cms-block card card-body mb-2 border-left border-primary" style="border-left-width:3px!important; background:#fff;">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="badge badge-primary">Text Paragraph</span>
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
    </div>
    <input type="hidden" name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][type]" value="text">
    <textarea class="form-control form-control-sm" rows="3"
              name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][content]"
              placeholder="Paragraph text...">{{ $block['content'] ?? '' }}</textarea>
</div>

@elseif($type === 'subheading')
<div class="cms-block card card-body mb-2 border-left border-secondary" style="border-left-width:3px!important; background:#fff;">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="badge badge-secondary">Subheading</span>
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
    </div>
    <input type="hidden" name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][type]" value="subheading">
    <div class="d-flex" style="gap:8px;">
        <select class="form-control form-control-sm" style="width:80px; flex-shrink:0;"
                name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][level]">
            <option value="h3" {{ ($block['level'] ?? 'h3') === 'h3' ? 'selected' : '' }}>H3</option>
            <option value="h4" {{ ($block['level'] ?? 'h3') === 'h4' ? 'selected' : '' }}>H4</option>
        </select>
        <input type="text" class="form-control form-control-sm"
               name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][text]"
               value="{{ $block['text'] ?? '' }}" placeholder="Subheading text">
    </div>
</div>

@elseif($type === 'table')
@php
    $tHeaders = $block['headers'] ?? ['Column 1', 'Column 2'];
    $tRows    = $block['rows'] ?? [[]];
@endphp
<div class="cms-block card card-body mb-2 border-left border-warning" style="border-left-width:3px!important; background:#fff;">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="badge badge-warning text-dark">Table</span>
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
    </div>
    <input type="hidden" name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][type]" value="table">
    <div class="row mb-2">
        <div class="col-md-4 form-group mb-2">
            <label class="text-muted" style="font-size:0.75rem;">Table Heading (optional)</label>
            <input type="text" class="form-control form-control-sm"
                   name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][heading]"
                   value="{{ $block['heading'] ?? '' }}" placeholder="Table heading">
        </div>
        <div class="col-md-2 form-group mb-2">
            <label class="text-muted" style="font-size:0.75rem;">Header BG</label>
            <div class="cms-color-row">
                <input type="color" class="cms-color-picker" value="{{ $block['header_bg'] ?? '#3b6fd4' }}"
                       name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][header_bg]">
                <input type="text" class="form-control form-control-sm color-hex" value="{{ $block['header_bg'] ?? '#3b6fd4' }}">
            </div>
        </div>
        <div class="col-md-2 form-group mb-2">
            <label class="text-muted" style="font-size:0.75rem;">Header Text</label>
            <div class="cms-color-row">
                <input type="color" class="cms-color-picker" value="{{ $block['header_text_color'] ?? '#ffffff' }}"
                       name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][header_text_color]">
                <input type="text" class="form-control form-control-sm color-hex" value="{{ $block['header_text_color'] ?? '#ffffff' }}">
            </div>
        </div>
        <div class="col-md-2 form-group mb-2">
            <label class="text-muted" style="font-size:0.75rem;">Alt Row BG</label>
            <div class="cms-color-row">
                <input type="color" class="cms-color-picker" value="{{ $block['alt_row_bg'] ?? '#f8f9fa' }}"
                       name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][alt_row_bg]">
                <input type="text" class="form-control form-control-sm color-hex" value="{{ $block['alt_row_bg'] ?? '#f8f9fa' }}">
            </div>
        </div>
        <div class="col-md-2 form-group mb-2">
            <label class="text-muted" style="font-size:0.75rem;">Border Color</label>
            <div class="cms-color-row">
                <input type="color" class="cms-color-picker" value="{{ $block['border_color'] ?? '#dee2e6' }}"
                       name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][border_color]">
                <input type="text" class="form-control form-control-sm color-hex" value="{{ $block['border_color'] ?? '#dee2e6' }}">
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm cms-editable-table mb-1">
            <thead>
                <tr class="cms-table-header-row">
                    @foreach($tHeaders as $ci => $hCell)
                    <th><input type="text" class="form-control form-control-sm"
                               name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][headers][{{ $ci }}]"
                               value="{{ $hCell }}" placeholder="Column {{ $ci + 1 }}"></th>
                    @endforeach
                    <th style="width:40px;"><button type="button" class="btn btn-sm btn-outline-danger js-remove-col" title="Remove last column">−col</button></th>
                </tr>
            </thead>
            <tbody class="cms-table-body">
                @foreach($tRows as $ri => $row)
                <tr class="cms-table-data-row">
                    @foreach($tHeaders as $ci => $hCell)
                    <td><input type="text" class="form-control form-control-sm"
                               name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][rows][{{ $ri }}][{{ $ci }}]"
                               value="{{ $row[$ci] ?? '' }}"></td>
                    @endforeach
                    <td><button type="button" class="btn btn-sm btn-outline-danger js-remove-row">×</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex" style="gap:8px;">
        <button type="button" class="btn btn-xs btn-outline-secondary js-add-col" style="font-size:0.75rem; padding:2px 8px;">+ Column</button>
        <button type="button" class="btn btn-xs btn-outline-secondary js-add-row" style="font-size:0.75rem; padding:2px 8px;">+ Row</button>
    </div>
</div>

@elseif($type === 'list')
<div class="cms-block card card-body mb-2 border-left border-info" style="border-left-width:3px!important; background:#fff;">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="badge badge-info">List</span>
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
    </div>
    <input type="hidden" name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][type]" value="list">
    <div class="cms-list-items-container mb-2">
        @foreach($block['items'] ?? [] as $ii => $item)
        <div class="d-flex mb-1" style="gap:8px;">
            <input type="text" class="form-control form-control-sm" style="max-width:140px;"
                   name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][items][{{ $ii }}][label]"
                   value="{{ $item['label'] ?? '' }}" placeholder="Bold label (optional)">
            <input type="text" class="form-control form-control-sm"
                   name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][items][{{ $ii }}][text]"
                   value="{{ $item['text'] ?? '' }}" placeholder="Item text">
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-list-item flex-shrink-0">×</button>
        </div>
        @endforeach
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary js-add-list-item">+ Add Item</button>
</div>

@elseif($type === 'callout')
<div class="cms-block card card-body mb-2 border-left border-success" style="border-left-width:3px!important; background:#fff;">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="badge badge-success">Callout Box</span>
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
    </div>
    <input type="hidden" name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][type]" value="callout">
    <div class="row mb-2">
        <div class="col-md-3 form-group mb-2">
            <label class="text-muted" style="font-size:0.75rem;">Background Color</label>
            <div class="cms-color-row">
                <input type="color" class="cms-color-picker" value="{{ $block['bg_color'] ?? '#eff3fb' }}"
                       name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][bg_color]">
                <input type="text" class="form-control form-control-sm color-hex" value="{{ $block['bg_color'] ?? '#eff3fb' }}">
            </div>
        </div>
        <div class="col-md-3 form-group mb-2">
            <label class="text-muted" style="font-size:0.75rem;">Left Border Color</label>
            <div class="cms-color-row">
                <input type="color" class="cms-color-picker" value="{{ $block['border_color'] ?? '#3b6fd4' }}"
                       name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][border_color]">
                <input type="text" class="form-control form-control-sm color-hex" value="{{ $block['border_color'] ?? '#3b6fd4' }}">
            </div>
        </div>
    </div>
    <div class="form-group mb-2">
        <label class="text-muted" style="font-size:0.75rem;">Callout Heading (optional)</label>
        <input type="text" class="form-control form-control-sm"
               name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][heading]"
               value="{{ $block['heading'] ?? '' }}" placeholder="e.g. Wie dr.fuxx helfen kann?">
    </div>
    <div class="form-group mb-0">
        <label class="text-muted" style="font-size:0.75rem;">Content</label>
        <textarea class="form-control form-control-sm" rows="3"
                  name="sections[medical_content][articles][{{ $ai }}][blocks][{{ $bi }}][content]"
                  placeholder="Callout body text...">{{ $block['content'] ?? '' }}</textarea>
    </div>
</div>
@endif
