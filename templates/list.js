{literal}
$(document).ready(function () {
  // console.log('{/literal}{$listurl}{literal}');
   $('#fuploader_{/literal}{$collection_id}{literal}').fineUploader({
     request: {
       endpoint: '{/literal}{$endpoint}{literal}',
       params: {/literal}{$params}{literal}
     },
     // debug: true,
     failedUploadTextDisplay: {
       mode: 'custom'
     },
     {/literal}{$fu_validation}{literal}
     text: {
       uploadButton: '{/literal}{$uploadButtonText}{literal}'
     }
   }).on('complete', function(event, id, fileName, responseJSON) {
     if (responseJSON.success) {
        // Refresh something
        $.ajax({
          url:'{/literal}{$listurl}{literal}'
        }).done(function(data){
          $('#collection_{/literal}{$collection_id}{literal}').replaceWith(data);
          $('#collection_{/literal}{$collection_id}{literal} .delete').each(function(){
            // console.log($(this));
            $(this).bind('click', function(){
              result = confirm('Do you really want to do that?');
              if(result)
              {
                ligne = $(this);
                // console.log(line);    
                $.ajax({
                  url: ligne.attr('href')
                }).done(function(data){
                  // console.log(line.parent().parent());
                  ligne.parent().parent().remove();
                  // line.parent().parent().effect('drop'); // DONTWORK
                });

                return false;          
              }
              else
              {
                return false;
              }

            });
          });
        });
      }
    });
    

    $("#collection_{/literal}{$collection_id}{literal} .sortable" ).sortable({
      opacity: 0.5,
      stop: function(){
        var order = $("#collection_{/literal}{$collection_id}{literal} .sortable" ).sortable('serialize', {
          attribute: 'data-id'
        });
        url = $(this).attr('data-url') + '&' + order;
        // console.log(url);
        $.ajax({
            url: url
          });
        
      }
    });
    // $( ".sortable" ).disableSelection();

    $('#collection_{/literal}{$collection_id}{literal} .editable').editable('{/literal}{$edit}{literal}', {
      id: '{/literal}{$id}{literal}media_id',
      name: '{/literal}{$id}{literal}value'
    });
    
    $('#collection_{/literal}{$collection_id}{literal} .delete').each(function(){
      // console.log($(this));
      $(this).bind('click', function(){
        result = confirm('Do you really want to do that?');
        if(result)
        {
          ligne = $(this);
          // console.log(line);    
          $.ajax({
            url: ligne.attr('href')
          }).done(function(data){
            ligne.parent().parent().hide('highlight');
            // ligne.parent().parent().remove();
          });
          
          return false;          
        }
        else
        {
          return false;
        }
        
      });
    });
    
 });
{/literal}