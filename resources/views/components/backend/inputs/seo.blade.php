<x-backend.inputs.text label="Meta Title" name="meta_title" placeholder="Type meta title" value="{!! $metaTitle !!}"
    :isRequired="false" aiGenerate="true" />
<x-backend.inputs.textarea label="Meta Description" name="meta_description" placeholder="Type meta description"
    value="{!! $metaDescription !!}" :isRequired="false" aiGenerate="true" />
<x-backend.inputs.textarea label="Meta Keywords" name="meta_keywords"
    placeholder="Type comma separated keywords. e.g. keyword1, keyword2" value="{!! $metaKeywords !!}"
    :isRequired="false" aiGenerate="true" />
<x-backend.inputs.file label="Meta Image" name="meta_image" value="{!! $metaImage !!}" :isRequired="false" />
