<?php



qode_startit_get_footer();



global $qode_startit_toolbar;

if(isset($qode_startit_toolbar)) include("toolbar.php");







?>



<script>

  jQuery('.carousel-inner').children('.item').eq(0).addClass('first_slider');

 </script>