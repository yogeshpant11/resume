<?php
/*
Plugin Name: Author List and Post
Version: 1.0
Author: Y.Pant
*/

Class Author_List_Post {

    public function __construct() {

            global $wpdb;
            $this->db = $wpdb;
            add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ) );
            add_shortcode( 'authorpost', array( $this, 'author_post' ) );
            add_action( 'wp_ajax_filter_post', array( $this, 'filter_post' ) );
            add_action( 'wp_ajax_nopriv_filter_post', array( $this, 'filter_post' ) );
    }

    public function register_script() {

            wp_register_script( 'author', plugins_url( '/js/author.js', __FILE__ ), array( 'jquery' ), '1.12.4', true );
            wp_localize_script( 'author', 'ajaxobject', array( 
                'ajaxurl' => admin_url( 'admin-ajax.php' ), 
                'nonce' => wp_create_nonce('nonce')
            ) );
            wp_enqueue_script( 'author' );
    }

    public function author_post() {
          
          $authors = $this->db->get_results( "SELECT * FROM `" . $this->db->prefix . "users` " );

          ob_start();

              echo "<select id='author_list'>";
              echo "<option value=''>Select Author</option>";
                  foreach( $authors as $author ) { ?>
                         <option value="<?php echo $author->ID; ?>"><?php echo $author->display_name; ?></option>
              <?php    }
              echo "</select>";
              echo "<div id='author-container'></div>";

          $output_string = ob_get_contents();
          ob_end_clean();

          return $output_string; // or just use ob_end_flush();
    }

    public function filter_post() {

        //checking the nonce. will die if it is no good.
        $valid_req = check_ajax_referer('nonce', 'nonce'); 

        if ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) ) {

            $uid = $_POST['id'];
            $result = array();
            $output = array();

            $args = array(
              'author'        =>  $uid, 
              'orderby'       =>  'post_date',
              'order'         =>  'DESC',
              'posts_per_page'  => 10
            );

            $user_posts = get_posts( $args );
            $total = count($user_posts);

            foreach ($user_posts as $post) {
               $output[] = '<a href="'.get_permalink( $post->ID ).'">'.get_the_title( $post->ID ).'</a><br/>';
               $output[] .= get_the_excerpt($post->ID)."<br/><br/>";
            }
            
            if( $total>0 ) {
                $result['response'] = "success";
                $result['data'] = $output;
            }

            else {
                $result['response'] = "success";
                $result['data'] = "No Post Found";
            }

            echo json_encode($result);
            die();
        }
    }

}

new Author_list_Post;