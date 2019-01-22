<?php 
/*
 Plugin Name: Multipurpose Photography Pro Posttype
 lugin URI: https://www.vwthemes.com/
 Description: Creating new post type for Multipurpose Photography Pro Theme.
 Author: Themesglance Themes
 Version: 1.0
 Author URI: https://www.themesglance.com/
*/

define( 'multipurpose_photography_pro_POSTTYPE_VERSION', '1.0' );
add_action( 'init', 'projectscategory');
add_action( 'init', 'multipurpose_photography_pro_posttype_create_post_type' );

function multipurpose_photography_pro_posttype_create_post_type() {
  register_post_type( 'services',
    array(
        'labels' => array(
            'name' => __( 'Services','multipurpose-photography-pro-posttype' ),
            'singular_name' => __( 'Services','multipurpose-photography-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-tag',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
        )
    )
  );

  register_post_type( 'projects',
    array(
        'labels' => array(
            'name' => __( 'Projects','multipurpose-photography-pro-posttype' ),
            'singular_name' => __( 'Projects','multipurpose-photography-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-tag',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
        )
    )
  );

  register_post_type( 'testimonials',
    array(
  		'labels' => array(
  			'name' => __( 'Testimonials','multipurpose-photography-pro-posttype' ),
  			'singular_name' => __( 'Testimonials','multipurpose-photography-pro-posttype' )
  		),
  		'capability_type' => 'post',
  		'menu_icon'  => 'dashicons-businessman',
  		'public' => true,
  		'supports' => array(
  			'title',
  			'editor',
  			'thumbnail'
  		)
		)
	);
  register_post_type( 'team',
    array(
      'labels' => array(
        'name' => __( 'Our Team','multipurpose-photography-pro-posttype' ),
        'singular_name' => __( 'Our Team','multipurpose-photography-pro-posttype' )
      ),
        'capability_type' => 'post',
        'menu_icon'  => 'dashicons-businessman',
        'public' => true,
        'supports' => array( 
          'title',
          'editor',
          'thumbnail'
      )
    )
  );
}

// --------------- Services ------------------
// Serives section
function multipurpose_photography_pro_posttype_images_metabox_enqueue($hook) {
  if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
    wp_enqueue_script('multipurpose-photography-pro-posttype-images-metabox', plugin_dir_url( __FILE__ ) . '/js/img-metabox.js', array('jquery', 'jquery-ui-sortable'));

    global $post;
    if ( $post ) {
      wp_enqueue_media( array(
          'post' => $post->ID,
        )
      );
    }

  }
}
add_action('admin_enqueue_scripts', 'multipurpose_photography_pro_posttype_images_metabox_enqueue');

function multipurpose_photography_pro_posttype_bn_meta_callback_services( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    
}

function multipurpose_photography_pro_posttype_bn_meta_save_services( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
 
  
}
add_action( 'save_post', 'multipurpose_photography_pro_posttype_bn_meta_save_services' );

/* Services shortcode */
function multipurpose_photography_pro_posttype_services_func( $atts ) {
  $services = '';
  $services = '<div id="services">
              <div class="row">';
  $query = new WP_Query( array( 'post_type' => 'services') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=services');

  while ($new->have_posts()) : $new->the_post();
        $custom_url ='';
        $post_id = get_the_ID();
        $excerpt = wp_trim_words(get_the_excerpt(),25);
        $services_image= get_post_meta(get_the_ID(), 'meta-image', true);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        if(get_post_meta($post_id,'meta-services-url',true !='')){$custom_url =get_post_meta($post_id,'meta-services-url',true); } else{ $custom_url = get_permalink(); }
        $services .= '<div class="col-lg-3 col-md-3 col-sm-4 col-6">
                        <div class="services-content">
                          <div class="row services-data">
                            <a href="'.esc_url($custom_url).'">
                              <h4 class="services-title">'.esc_html(get_the_title()) .'</h4>
                            </a>
                          </div>
                          <div class="services-img">
                            <img src="'.esc_url($thumb_url).'" />
                          </div>
                        </div>
                      </div>';
    if($k%2 == 0){
      $services.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $services = '<h2 class="center">'.esc_html__('Post Not Found','multipurpose-photography-pro-posttype').'</h2>';
  endif;
  $services .= '</div></div>';
  return $services;
}

add_shortcode( 'list-services', 'multipurpose_photography_pro_posttype_services_func' );


// ----------------- latestworks Meta ---------------------
function multipurpose_photography_pro_posttype_bn_custom_meta_projects() {

    add_meta_box( 'bn_meta', __( 'Project Meta', 'multipurpose-photography-pro-posttype' ), 'multipurpose_photography_pro_posttype_bn_meta_callback_projects', 'projects', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
  add_action('admin_menu', 'multipurpose_photography_pro_posttype_bn_custom_meta_projects');
}

function multipurpose_photography_pro_posttype_bn_meta_callback_projects( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $project_name = get_post_meta( $post->ID, 'meta-project-name', true );
    ?>
  <div id="property_stuff">
    <table id="list-table">     
      <tbody id="the-list" data-wp-lists="list:meta">
      
        <tr id="meta-2">
          <td class="left">
            <?php esc_html_e( 'Project Name', 'multipurpose-photography-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-project-name" id="meta-project-name" value="<?php echo esc_attr( $project_name ); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

function projectscategory() {
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'              => __( 'Categories', 'multipurpose-photography-pro-posttype' ),
    'singular_name'     => __( 'Categories', 'multipurpose-photography-pro-posttype' ),
    'search_items'      => __( 'Search cats', 'multipurpose-photography-pro-posttype' ),
    'all_items'         => __( 'All Categories', 'multipurpose-photography-pro-posttype' ),
    'parent_item'       => __( 'Parent Categories', 'multipurpose-photography-pro-posttype' ),
    'parent_item_colon' => __( 'Parent Categories:', 'multipurpose-photography-pro-posttype' ),
    'edit_item'         => __( 'Edit Categories', 'multipurpose-photography-pro-posttype' ),
    'update_item'       => __( 'Update Categories', 'multipurpose-photography-pro-posttype' ),
    'add_new_item'      => __( 'Add New Categories', 'multipurpose-photography-pro-posttype' ),
    'new_item_name'     => __( 'New Categories Name', 'multipurpose-photography-pro-posttype' ),
    'menu_name'         => __( 'Categories', 'multipurpose-photography-pro-posttype' ),
  );
  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'projectscategory' ),
  );
  register_taxonomy( 'projectscategory', array( 'projects' ), $args );
}


function multipurpose_photography_pro_posttype_bn_meta_save_projects( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  
  // Save Course Duration
  if( isset( $_POST[ 'meta-project-name' ] ) ) {
    update_post_meta( $post_id, 'meta-project-name', sanitize_text_field($_POST[ 'meta-project-name' ]) );
  }
}
add_action( 'save_post', 'multipurpose_photography_pro_posttype_bn_meta_save_projects' );


/* --------------------- projects shortcode  ------------------- */

function multipurpose_photography_pro_posttype_projects_func( $atts ) {
  $projects = '';
  $projects = '<div id="projects">
                <div class="row">';
  $query = new WP_Query( array( 'post_type' => 'projects') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=projects');

  while ($new->have_posts()) : $new->the_post();
        $custom_url ='';
        $post_id = get_the_ID();
        $projectname= get_post_meta($post_id,'meta-project-name',true);
        $excerpt = wp_trim_words(get_the_excerpt(),25);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        
        if(get_post_meta($post_id,'meta-projects-url',true !='')){$custom_url =get_post_meta($post_id,'meta-projects-url',true); } else{ $custom_url = get_permalink(); }
        $projects .= '<div class="col-lg-3 col-md-4 col-sm-6 col-6 p-0 box project-image">
                        <img src="'.esc_url($thumb_url).'">
                        <div class="box-content">
                          <div class="project_content">
                            <h5 class="title">
                              <a href="'.get_the_permalink().'">
                                '.get_the_title().'
                              </a>
                            </h5>
                            <span>'.$projectname.'</span>
                          </div>
                        </div>
                      </div>';
    if($k%2 == 0){
      $projects.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $projects = '<h2 class="center">'.esc_html__('Post Not Found','multipurpose-photography-pro-posttype-pro').'</h2>';
  endif;
  $projects .= '</div>
                </div>';
  return $projects;
}

add_shortcode( 'list-projects', 'multipurpose_photography_pro_posttype_projects_func' );



/*------------------ Testimonial section -------------------*/

/* Adds a meta box to the Testimonial editing screen */
function multipurpose_photography_pro_posttype_bn_testimonial_meta_box() {
	add_meta_box( 'multipurpose-photography-pro-posttype-testimonial-meta', __( 'Enter Details', 'multipurpose-photography-pro-posttype' ), 'multipurpose_photography_pro_posttype_bn_testimonial_meta_callback', 'testimonials', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'multipurpose_photography_pro_posttype_bn_testimonial_meta_box');
}

/* Adds a meta box for custom post */
function multipurpose_photography_pro_posttype_bn_testimonial_meta_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'multipurpose_photography_pro_posttype_posttype_testimonial_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
	$desigstory = get_post_meta( $post->ID, 'multipurpose_photography_pro_posttype_testimonial_desigstory', true );
	?>
	<div id="testimonials_custom_stuff">
		<table id="list">
			<tbody id="the-list" data-wp-lists="list:meta">
				<tr id="meta-1">
					<td class="left">
						<?php _e( 'Designation', 'multipurpose-photography-pro-posttype' )?>
					</td>
					<td class="left" >
						<input type="text" name="multipurpose_photography_pro_posttype_testimonial_desigstory" id="multipurpose_photography_pro_posttype_testimonial_desigstory" value="<?php echo esc_attr( $desigstory ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}

/* Saves the custom meta input */
function multipurpose_photography_pro_posttype_bn_metadesig_save( $post_id ) {
	if (!isset($_POST['multipurpose_photography_pro_posttype_posttype_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['multipurpose_photography_pro_posttype_posttype_testimonial_meta_nonce'], basename(__FILE__))) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// Save desig.
	if( isset( $_POST[ 'multipurpose_photography_pro_posttype_testimonial_desigstory' ] ) ) {
		update_post_meta( $post_id, 'multipurpose_photography_pro_posttype_testimonial_desigstory', sanitize_text_field($_POST[ 'multipurpose_photography_pro_posttype_testimonial_desigstory']) );
	}

}

add_action( 'save_post', 'multipurpose_photography_pro_posttype_bn_metadesig_save' );

/*---------------Testimonials shortcode -------------------*/
function multipurpose_photography_pro_posttype_testimonial_func( $atts ) {

    $testimonial = ''; 
    $testimonial = '<div id="testimonials">
                    <div class="row">';
    $custom_url = '';
      $new = new WP_Query( array( 'post_type' => 'testimonials' ) );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $excerpt = wp_trim_words(get_the_excerpt(),25);
          if(has_post_thumbnail()) {
            $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
            $thumb_url = $thumb['0'];
          }
          $desigstory= get_post_meta($post_id,'multipurpose_photography_pro_posttype_testimonial_desigstory',true);
            $testimonial .= '<div class="col-lg-4 col-md-4 col-sm-6 col-12">
                              <div class="testi-data box-testi"> 
                                <div class="testimonial_box w-100 mb-3" >
                                  <div class="content_box testi-padding">
                                    <div class="short_text pb-3">
                                      <i class="fas fa-quote-left"></i> '.$excerpt.' <i class="fas fa-quote-right"></i>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12 textimonial-img">
                                        <img src="'.esc_url($thumb_url).'">
                                    </div>
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-12 testimonial-box">
                                      <h4 class="testimonial_name"><a href="'.get_permalink().'">'.get_the_title().'</a></h4>
                                      <span class="t-desig">
                                      '.$desigstory.'
                                      </span>
                                    </div>
                                  </div>
                                </div>
                              </div>';  
            $testimonial .= '</div>';

            if($k%3 == 0){
                $testimonial.= '<div class="clearfix"></div>'; 
            } 
          $k++;         
        endwhile; 
        wp_reset_postdata();
      else :
        $project = '<h2 class="center">'.__('Not Found','multipurpose-photography-pro-posttype').'</h2>';
      endif;
    $testimonial.= '</div></div>';
  return $testimonial;
  //
}
add_shortcode( 'list-testimonials', 'multipurpose_photography_pro_posttype_testimonial_func' );

/*--------------Team -----------------*/
/* Adds a meta box for Designation */
function multipurpose_photography_pro_posttype_bn_team_meta() {
    add_meta_box( 'multipurpose_photography_pro_posttype_bn_meta', __( 'Enter Details','multipurpose-photography-pro-posttype' ), 'multipurpose_photography_pro_posttype_ex_bn_meta_callback', 'team', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'multipurpose_photography_pro_posttype_bn_team_meta');
}
/* Adds a meta box for custom post */
function multipurpose_photography_pro_posttype_ex_bn_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'multipurpose_photography_pro_posttype_bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );

    //Email details
    if(!empty($bn_stored_meta['meta-desig'][0]))
      $bn_meta_desig = $bn_stored_meta['meta-desig'][0];
    else
      $bn_meta_desig = '';

    //Phone details
    if(!empty($bn_stored_meta['meta-call'][0]))
      $bn_meta_call = $bn_stored_meta['meta-call'][0];
    else
      $bn_meta_call = '';


    //facebook details
    if(!empty($bn_stored_meta['meta-facebookurl'][0]))
      $bn_meta_facebookurl = $bn_stored_meta['meta-facebookurl'][0];
    else
      $bn_meta_facebookurl = '';


    //linkdenurl details
    if(!empty($bn_stored_meta['meta-linkdenurl'][0]))
      $bn_meta_linkdenurl = $bn_stored_meta['meta-linkdenurl'][0];
    else
      $bn_meta_linkdenurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-twitterurl'][0]))
      $bn_meta_twitterurl = $bn_stored_meta['meta-twitterurl'][0];
    else
      $bn_meta_twitterurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-googleplusurl'][0]))
      $bn_meta_googleplusurl = $bn_stored_meta['meta-googleplusurl'][0];
    else
      $bn_meta_googleplusurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-designation'][0]))
      $bn_meta_designation = $bn_stored_meta['meta-designation'][0];
    else
      $bn_meta_designation = '';

    ?>
    <div id="agent_custom_stuff">
        <table id="list-table">         
            <tbody id="the-list" data-wp-lists="list:meta">
                <tr id="meta-1">
                    <td class="left">
                        <?php _e( 'Email', 'multipurpose-photography-pro-posttype' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="meta-desig" id="meta-desig" value="<?php echo esc_attr($bn_meta_desig); ?>" />
                    </td>
                </tr>
                <tr id="meta-2">
                    <td class="left">
                        <?php _e( 'Phone Number', 'multipurpose-photography-pro-posttype' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="meta-call" id="meta-call" value="<?php echo esc_attr($bn_meta_call); ?>" />
                    </td>
                </tr>
                <tr id="meta-3">
                  <td class="left">
                    <?php _e( 'Facebook Url', 'multipurpose-photography-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-facebookurl" id="meta-facebookurl" value="<?php echo esc_url($bn_meta_facebookurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-4">
                  <td class="left">
                    <?php _e( 'Linkedin URL', 'multipurpose-photography-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-linkdenurl" id="meta-linkdenurl" value="<?php echo esc_url($bn_meta_linkdenurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-5">
                  <td class="left">
                    <?php _e( 'Twitter Url', 'multipurpose-photography-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-twitterurl" id="meta-twitterurl" value="<?php echo esc_url( $bn_meta_twitterurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-6">
                  <td class="left">
                    <?php _e( 'GooglePlus URL', 'multipurpose-photography-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-googleplusurl" id="meta-googleplusurl" value="<?php echo esc_url($bn_meta_googleplusurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-7">
                  <td class="left">
                    <?php _e( 'Designation', 'multipurpose-photography-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="text" name="meta-designation" id="meta-designation" value="<?php echo esc_attr($bn_meta_designation); ?>" />
                  </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}
/* Saves the custom Designation meta input */
function multipurpose_photography_pro_posttype_ex_bn_metadesig_save( $post_id ) {
    if( isset( $_POST[ 'meta-desig' ] ) ) {
        update_post_meta( $post_id, 'meta-desig', esc_html($_POST[ 'meta-desig' ]) );
    }
    if( isset( $_POST[ 'meta-call' ] ) ) {
        update_post_meta( $post_id, 'meta-call', esc_html($_POST[ 'meta-call' ]) );
    }
    // Save facebookurl
    if( isset( $_POST[ 'meta-facebookurl' ] ) ) {
        update_post_meta( $post_id, 'meta-facebookurl', esc_url($_POST[ 'meta-facebookurl' ]) );
    }
    // Save linkdenurl
    if( isset( $_POST[ 'meta-linkdenurl' ] ) ) {
        update_post_meta( $post_id, 'meta-linkdenurl', esc_url($_POST[ 'meta-linkdenurl' ]) );
    }
    if( isset( $_POST[ 'meta-twitterurl' ] ) ) {
        update_post_meta( $post_id, 'meta-twitterurl', esc_url($_POST[ 'meta-twitterurl' ]) );
    }
    // Save googleplusurl
    if( isset( $_POST[ 'meta-googleplusurl' ] ) ) {
        update_post_meta( $post_id, 'meta-googleplusurl', esc_url($_POST[ 'meta-googleplusurl' ]) );
    }
    // Save designation
    if( isset( $_POST[ 'meta-designation' ] ) ) {
        update_post_meta( $post_id, 'meta-designation', esc_html($_POST[ 'meta-designation' ]) );
    }
}
add_action( 'save_post', 'multipurpose_photography_pro_posttype_ex_bn_metadesig_save' );

add_action( 'save_post', 'bn_meta_save' );
/* Saves the custom meta input */
function bn_meta_save( $post_id ) {
  if( isset( $_POST[ 'multipurpose_photography_pro_posttype_team_featured' ] )) {
      update_post_meta( $post_id, 'multipurpose_photography_pro_posttype_team_featured', esc_attr(1));
  }else{
    update_post_meta( $post_id, 'multipurpose_photography_pro_posttype_team_featured', esc_attr(0));
  }
}
/*------------ SHORTCODES ----------------*/

/*------------- Team Shorthcode -------------*/
function multipurpose_photography_pro_posttype_team_func( $atts ) {
    $team = ''; 
    $team = '<div id="team">
              <div class="row">';
      $new = new WP_Query( array( 'post_type' => 'team') );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
          $thumb_url = $thumb['0'];
          $excerpt = multipurpose_photography_pro_string_limit_words(get_the_excerpt(),20);
          $designation = get_post_meta($post_id,'meta-designation',true);
          $call = get_post_meta($post_id,'meta-call',true);
          $facebookurl = get_post_meta($post_id,'meta-facebookurl',true);
          $linkedin = get_post_meta($post_id,'meta-linkdenurl',true);
          $twitter = get_post_meta($post_id,'meta-twitterurl',true);
          $googleplus = get_post_meta($post_id,'meta-googleplusurl',true);

          $team .= '<div class="team_outer col-lg-3 col-sm-6 mb-4">
                      <div class="team-content">
                        <div class="row team-data">
                          <a href="'.get_the_permalink().'">
                            <h4 class="team-title">'.get_the_title().'</h4>
                          </a>
                        </div>
                        <div class="team_wrap">
                          <img src="'.esc_url($thumb_url).'"> 
                        </div>
                      </div>';
            $team .='</div>';     
          if($k%4 == 0){
              $team.= '<div class="clearfix"></div>'; 
          } 
          $k++;         
        endwhile; 
        wp_reset_postdata();
        $team.= '</div></div>';
      else :
        $team = '<div id="team" class="team_wrap col-md-3 mt-3 mb-4"><h2 class="center">'.__('Not Found','multipurpose-photography-pro-posttype').'</h2></div>';
      endif;
    return $team;
}
add_shortcode( 'list-team', 'multipurpose_photography_pro_posttype_team_func' );
