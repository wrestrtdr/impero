tinymce.init({
        mode : "specific_textareas",
        relative_urls: false,
        convert_urls: false,
        remove_script_host : false,
        selector : ".mceEditor",
		element_format : "xhtml",
		schema: "html5",
		entity_encoding : "raw",
        style_formats: [
	        {title: 'H1', block: 'h1'},
	        {title: 'H2', block: 'h2'},
	        {title: 'H3', block: 'h3'},
	        {title: 'Normal', inline: 'span'},
	        {title: 'Extra small', classes: 'fsXS', inline: 'span'},
	        {title: 'Small', classes: 'fsS', inline: 'span'},
	        {title: 'Medium', classes: 'fsM', inline: 'span'},
	        {title: 'Large', classes: 'fsL', inline: 'span'},
	        {title: 'Extra large', classes: 'fsXL', inline: 'span'},
	        {title: 'Extra extra large', classes: 'fsXXL', inline: 'span'},
	        {title: 'Black', classes: 'clrBlack', inline: 'span'},
	        {title: 'White', classes: 'clrWhite', inline: 'span'},
	        {title: 'Red', classes: 'clrRed', inline: 'span'},
	        {title: 'Green', classes: 'clrGreen', inline: 'span'},
	        {title: 'Blue', classes: 'clrBlue', inline: 'span'},
	        {title: 'Yellow', classes: 'clrYellow', inline: 'span'},
	        {title: 'Orange', classes: 'clrOrange', inline: 'span'}
	    ],
	    plugins: [
						"advlist autolink lists link image charmap print preview anchor paste",
						"searchreplace visualblocks code fullscreen",
						"insertdatetime media table contextmenu"
					],
	    toolbar: "bold italic underline strikethrough superscript subscript | styleselect forecolor fontsize | alignleft aligncenter alignright alignjustify | selectall cut copy paste | link image media table charmap | blockquote numlist bullist | undo redo removeformat code",
        menubar: " ",
        content_css : "/css/tinymce.css",
        setup : function(ed) {
			ed.on('change', function(e) {
				if (typeof validator !== "undefined") {
					$("#" + e.target.id).html(tinymce.activeEditor.getContent({format : 'raw'}));
					validator._validateForm();
				}
			});
			ed.on('init', function(e) {
				ed.pasteAsPlainText = true;
			});
        },
	    
	    paste_text_sticky : true,
	    convert_fonts_to_spans: false
});