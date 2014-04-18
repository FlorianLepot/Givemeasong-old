function aButtonPressed(){
  $.post('{{path('gmas_music_songs_ajax_suggest')}}',
    { youtubeid: 'youtubeid', category:'category' },
    function(response){
      if(response.code == 100 && response.success){
        alert('coucou : ' + response.category + ' - ' + response.youtubeid);
      }
    }, "json");
}

$(document).ready(function() {
  $('#suggest').on('click', function(){aButtonPressed();});
});
