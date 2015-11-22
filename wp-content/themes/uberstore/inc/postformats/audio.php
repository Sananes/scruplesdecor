<?php wp_enqueue_script('jplayer'); ?>
<?php $mp3 = get_post_meta($post->ID, 'post_audio_mp3', TRUE);
			$ogg = get_post_meta($post->ID, 'post_audio_ogg', TRUE); ?>
<div class="post-gallery audio">
	<div id="jplayer_<?php echo $post->ID; ?>" class="jp-jplayer jp-jplayer-audio" data-interface="#jp_interface_<?php echo $post->ID; ?>" data-mp3="<?php echo $mp3; ?>" data-ogg="<?php echo $ogg; ?>" data-swf="<?php echo THB_THEME_ROOT; ?>/assets/js"></div>
  <div class="jp-audio-container">
      <div class="jp-audio">
          <div id="jp_interface_<?php echo $post->ID; ?>" class="jp-interface">
              <ul class="jp-controls">
                  <li><a href="#" class="jp-play" tabindex="1"><i class="fa fa-play"></i></a></li>
                  <li><a href="#" class="jp-pause" tabindex="1"><i class="fa fa-pause"></i></a></li>
                  <li><a href="#" class="jp-mute" tabindex="1"><i class="fa fa-volume-up"></i></a></li>
                  <li><a href="#" class="jp-unmute" tabindex="1"><i class="fa fa-volume-off"></i></a></li>
              </ul>
              <div class="jp-time-holder">
                <div class="jp-current-time"></div>
                <div class="jp-duration"></div>
              </div>
              <div class="jp-progress">
                  <div class="jp-seek-bar">
                      <div class="jp-play-bar"></div>
                  </div>
              </div>
              <div class="jp-volume-bar-container">
                  <div class="jp-volume-bar">
                      <div class="jp-volume-bar-value"></div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>