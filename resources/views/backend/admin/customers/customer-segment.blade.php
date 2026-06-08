@foreach ($segments as $segment)
    <div class="flex items-center gap-3">
        <div class="flex-1"><x-backend.inputs.text class="input" name="name"  data-segment-id="{{ $segment->id }}" placeholder="Type segment name" value="{{ $segment->name }}" /></div>
        <div class="flex-1"><x-backend.inputs.number class="input" name="value"  data-segment-id="{{ $segment->id }}" placeholder="Type value" value="{{ $segment->value }}" min="0" step="0.001" /></div>
        <button class="ms-1 btn bg-red-100 text-red-500 rounded-md p-2.5 px-10 removeSegment" data-segment-id="{{ $segment->id }}" type="button">{{ translate("Remove") }}</button>
    </div>
@endforeach
