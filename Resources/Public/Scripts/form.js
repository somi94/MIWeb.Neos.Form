(function($) {
    var miUpload = function(elem, options) {
        this.that = this;
        this.container = $(elem);
        this.options = options;
    };

    miUpload.prototype = {
        defaultOptions: {
            dragAndDropText: 'Drag & Drop Files Or Click Here',
            allowDuplicateFiles: false,
            maxFiles: 0,
            inputName: 'files',
            allowedFiles: []
        },
        init: function() {
            var that = this;

            this.options = $.extend({}, this.defaultOptions, this.options, this.container.data('options'));

            if(this.container.is('input[type="file"]')) {
                var input = this.container;

                this.options.inputName = input.attr('name');

                this.container = $('<div></div>', {
                    'class': 'miupload'
                }).insertAfter(input);
                //input.appendTo(this.container);
                input.remove();
            }

            if(this.options.inputName.endsWith('[]')) {
                this.options.inputName = this.options.inputName.substr(0, this.options.inputName.length - 2);
            }

            this.container.addClass('miupload');
            this.container.append($('<div />',{
                'class': 'miupload-dragndrop',
                'text': that.options.dragAndDropText
            }).append($('<input />',{
                'type': 'file',
                'accept': 'pdf, image/*',
                'class': 'miupload-file-add'
            }))).append($('<div />',{
                'class': 'miupload-preview-container'
            }));

            /*$(document).on({
                'dragenter': function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                },
                'dragover': function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                },
                'drop': function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            });

            this.container.find('.miupload-dragndrop').on({
                'click': function(e) {
                    $(this).parent().find('.miupload-file-add').click();
                },
                'dragenter': function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    $(this).addClass('dragover');
                },
                'dragexit': function(e) {
                    e.stopPropagation();
                    e.preventDefault();

                    $(this).removeClass('dragover');
                },
                'dragover': function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                },
                'drop': function(e) {
                    e.preventDefault();

                    $(this).removeClass('dragover');

                    that.addFile(e.originalEvent.dataTransfer);
                }
            });*/

            this.container.find('.miupload-file-add').change(function() {
                var accept = ['pdf','jpeg','jpg'];
                var name = $(this).val().substring($(this).val().lastIndexOf(".")+1);
                if(accept.indexOf(name) != -1){
                    that.addFile(this);
                    //				console.log($(this).val());
                    $(this).val('');
                }else{
                    alert('Diese Datei wird nicht akzeptiert.');
                }
            });
        },
        addFile: function(input) {
            var name = $(input).val().substring($(input).val().lastIndexOf("\\")+1);
            var that = this;
            var inputClone = $(input).clone().attr('name',this.options.inputName + '[]').removeClass('miupload-file-add').addClass('file-input');
            var reader = new FileReader();
            reader.onload = function(e) {

                var src = e.target.result;
                var hash = that.hashCode(src);

                if(that.options.maxFiles > 0 && that.container.find('.miupload-preview-container .file-entry').length >= that.options.maxFiles) {
                    return;
                }
                if(!that.options.allowDuplicateFiles && that.container.find('.miupload-preview-container [data-hash="' + hash + '"]').length) {
                    return;
                }
                var preview = 'url(/_Resources/Static/Packages/MIWeb.Neos.Form/Images/file.jpg)';
                if(src.indexOf('image/') != -1){
                    preview = 'url(' + src + ')';
                }else{
                    if(src.indexOf('pdf') != -1){
                        preview = 'url(/_Resources/Static/Packages/MIWeb.Neos.Form/Images/pdf.jpg)';
                    }
                }

                $('<div />',{
                    'class': 'file-entry',
                    'data-hash': hash,
                    'css':{
                        'position': 'relative',
                        'padding-bottom': 20 + 'px'
                    }
                }).append($('<div />',{
                    'class': 'file-preview',
                    'css': {
                        'background-image': preview
                    }
                })).append($('<div />',{
                    'class': 'file-titel',
                    'text': name,
                    'css': {
                        'text-align': 'center',
                        'position': 'absolute',
                        'bottom': 5 + 'px',
                        'left': 5 + 'px',
                        'right': 5 + 'px',
                        'word-wrap':'break-word'
                    }
                })).append(inputClone).append($('<a />',{
                    'class': 'file-remove',
                    'text': 'X',
                    'click': function() {
                        $(this).parent().remove();
                    }
                })).appendTo(that.container.find('.miupload-preview-container'));
            };
            reader.readAsDataURL(input.files[0]);
        },
        hashCode: function(str) {
            var hash = 0, i, chr;
            if(str.length === 0) return hash;
            for(i = 0; i < str.length; i++) {
                chr   = str.charCodeAt(i);
                hash  = ((hash << 5) - hash) + chr;
                hash |= 0;
            }
            return hash;
        }
    };

    $.fn.miUpload = function(arg) {
        return this.each(function () {
            new miUpload(this,arg).init();
        });
        /*if(typeof arg === 'string' || arg instanceof String) {
            var method = this[arg];
            if(!!(method && method.constructor && method.call && method.apply)) {
                method.apply(this,Array.prototype.slice.call(arguments, 1));
            }
        } else {
            this.init(arg);
        }*/
    };
}( jQuery ));

(function($) {
    $('[data-miupload]').miUpload({
        dragAndDropText: 'Hier klicken oder Datei ablegen. (PDF oder JPG sind möglich)',
        inputName: 'upload'
    });
}( jQuery ));