$(function(){

    var ul = $('#upload ul');
    var dropZone = $('#drop');
    var btnContinue = $('.hidden-object');

    // Добавляем класс hover при наведении
    dropZone[0].ondragover = function() {
        dropZone.addClass('hover');
        return false;
    };

    // Убираем класс hover
    dropZone[0].ondragleave = function() {
        dropZone.removeClass('hover');
        return false;
    };

    $('#drop a').click(function(){
        // Simulate a click on the file input button
        // to show the file browser dialog
        $(this).parent().find('input').click();
    });

    // Initialize the jQuery File Upload plugin
    $('#upload').fileupload({
          

        // This element will accept file drag/drop uploading
        dropZone: $('#drop'),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {

        //Добавляем проверку расширения и размера файла        
        if (data.files[0].type != 'text/xml') {
          alert('Файл "' + data.files[0].name + '" неподходящего формата!');
          dropZone.removeClass('hover');
        return;}

        if (data.files[0].size > '1048576') {
          alert('Файл "' + data.files[0].name + '" слишком большой!');
          dropZone.removeClass('hover');
        return;}
 
          // Убираем класс hover
          dropZone.removeClass('hover');

          //Показываем кнопку "Продолжить"
          btnContinue.removeClass('hidden-object');

            var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
                ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

            // Append the file name and file size
            tpl.find('p').text(data.files[0].name)
                         .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

            // Add the HTML to the UL element
            data.context = tpl.appendTo(ul);

            // Initialize the knob plugin
            tpl.find('input').knob();

            // Listen for clicks on the cancel icon
            //Удаляем накладные
            tpl.find('span').click(function(){

                if(tpl.hasClass('working')){
                    jqXHR.abort();
                }

                tpl.fadeOut(function(){
                    tpl.remove();
                });

                //Считаем количество добавленных накладных для обработки
                var counter = $('canvas');

                //Скрываем кнопку "Продолжить" если это последняя накладная (которую мы удаляем)
                if (counter.length == 1){
                  btnContinue.addClass('hidden-object');
                }
            });

            // Automatically upload the file once it is added to the queue
            var jqXHR = data.submit();
        },

        progress: function(e, data){

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();

            if(progress == 100){
                data.context.removeClass('working');
            }
        },

        fail:function(e, data){
            // Something has gone wrong!
            data.context.addClass('error');
        }

    });

    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    // Helper function that formats the file sizes
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }

});