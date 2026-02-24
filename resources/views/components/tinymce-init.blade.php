<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '.wysiwyg-editor',
        height: 300,
        menubar: false,
        plugins: ['advlist', 'autolink', 'lists', 'link', 'charmap', 'preview', 'code', 'help'],
        toolbar: 'undo redo | formatselect | bold italic underline | ' +
                 'alignleft aligncenter alignright | bullist numlist | link | code | help',

        // Match HTMLPurifier allowed tags
        valid_elements: 'p,br,strong,em,u,a[href|title|target],ul,ol,li,h1,h2,h3,h4,h5,h6,blockquote,code,pre,img[src|alt],span[style]',

        forced_root_block: 'p',
        link_target_list: [{title: 'None', value: ''}, {title: 'New window', value: '_blank'}],
        default_link_target: '_blank',

        paste_word_valid_elements: 'p,br,strong,em,u,a,ul,ol,li,h1,h2,h3,h4,h5,h6',
        branding: false,
        promotion: false
    });
});
</script>
