define(['jquery','module/CSRFCheckManager'], function($,CSRFM) {
    var m = function() {

        var CSRFCheckManager = new CSRFM();

        function init(){
            CSRFCheckManager.init();

            $(document).on('change', '#file',  function(event) {
                event.preventDefault();

                $(".ocrcontent_inner").text(""); 
                $("#ocrcontent").hide();
                $("#ocrresult").text("");
                $("#ocrresult").removeClass("alert alert-danger");
                var fd = new FormData();
                var files = $('#file')[0].files;
                var profilepic_card_id = $('#profilepic_card_id').val();
                if(files.length > 0 ){
                   fd.append('profilepic_card_id',profilepic_card_id);
                   fd.append('file',files[0]);
                   $.ajax({
                      url: rootUrl+'api/uploadProfilePic',
                      type: 'post',
                      data: fd,
                      contentType: false,
                      processData: false,
                      beforeSend: function(){
                        $("#ocrimage").hide();
                      },
                      complete: function(){
                        $("#ocrimage").show();
                      },
                      success: function(response){
                         if(response != 0){
                             if(response.error == true)
                                $("#ocrresult").text(response.data); 
                            else{
                                $("#lettersimage").hide();
                                
                                 $("#ocrimage").attr("src",rootUrl+"download_img.php?img_name="+response.data.pic_big_name);
                            }
                         }else{
                            alert('file not uploaded');
                         }
                      },
                       error: function(e){
                           console.log(e);
                               $("#ocrresult").text("Non è stato possibile riconoscere il file." +
                                   "Riprova caricando file con estensione JPG, PNG o GIF con dimensione massima di 4 MB");
                           $("#ocrresult").addClass("alert alert-danger");
                           return false;
                       }
                   });
                }else{
                   alert("Please select a file.");
                }
            });

        }

        return {
            init: init
        };
    };

    return m;
});