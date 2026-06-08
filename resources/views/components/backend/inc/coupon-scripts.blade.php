<script>
    "use strict";

    // add
    function addCondition($this) {
        let html = `<div class="flex items-center mb-2">
                        <input type="text" name="infos[]" class="theme-input"
                            placeholder="{{ translate('Type your text') }}" />
                         <button class="ms-1 btn bg-secondary/10 text-red-500 rounded-md p-3" type="button" onclick="removeCondition(this)">x</button> 
                    </div>`;
        $($this).closest('.theme-input-wrapper').append(html);
    }

    // remove
    function removeCondition($this) {
        $this.closest('.flex').remove();
    }
</script>
