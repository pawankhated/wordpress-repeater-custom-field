<?php

//making the meta box (Note: meta box != custom meta field)
function wpse_add_custom_meta_box_for_services() {
  add_meta_box(
      'custom_meta_box-2',       // $id
      'Select Services',                  // $title
      'show_list_of_services',  // $callback
      'facilities',                 // $page
      'normal',                  // $context
      'high'                     // $priority
  );
}
add_action('add_meta_boxes', 'wpse_add_custom_meta_box_for_services');

//showing custom form fields
function show_list_of_services() {
  global $post;

  $data=get_post_meta(get_the_id(), 'job_choosed_servces');
  $setResponse=[];
  if(count($data)>0){
    $updatedData=$data[0];
    
    
    for($j=0;$j<count($updatedData);$j++){
        $keyval=explode("_parent_",$updatedData[$j]);
        $setResponse[$keyval[0]][] = $keyval[1];
    }
  }

  // echo "<pre>";print_r($setResponse);die;

  // Use nonce for verification to secure data sending
  wp_nonce_field( basename( __FILE__ ), 'wpse_our_nonce' );
  $hc_service_name = get_field('hc-service-name', 'option');
  $hc_sub_services = get_field('hc-sub-service-name', 'option');

  $of_service_name = get_field('of-service-name', 'option');
  $of_sub_service_name = get_field('of-sub-service-name', 'option');


  $ws_service_name = get_field('ws-service-name', 'option');
  $ws_sub_service_name = get_field('ws-sub-service-name', 'option');

  $gr_service_name = get_field('gr-service-name', 'option');
  $gr_sub_service_name = get_field('gr-sub-service-name', 'option');



  $selectBoxObj=[];
  if(!empty($hc_service_name)){
    $hc_service_name_slug = str_replace(" ", "-", strtolower($hc_service_name));
    $selectBoxObj[$hc_service_name_slug]['name']=$hc_service_name;
    $selectBoxObj[$hc_service_name_slug]['obj']=$hc_sub_services;
  }

  if(!empty($of_service_name)){
    $of_service_name_slug = str_replace(" ", "-", strtolower($of_service_name));
    $selectBoxObj[$of_service_name_slug]['name']=$of_service_name;
    $selectBoxObj[$of_service_name_slug]['obj']=$of_sub_service_name;
  }



  if(!empty($ws_service_name)){
    $hc_service_name_slug = str_replace(" ", "-", strtolower($ws_service_name));
    $selectBoxObj[$hc_service_name_slug]['name']=$ws_service_name;
    $selectBoxObj[$hc_service_name_slug]['obj']=$hc_sub_services;
  }

  if(!empty($gr_service_name)){
    $gr_service_name_slug = str_replace(" ", "-", strtolower($gr_service_name));
    $selectBoxObj[$gr_service_name_slug]['name']=$gr_service_name;
    $selectBoxObj[$gr_service_name_slug]['obj']=$gr_sub_service_name;
  }
  

 $customObjSelectBox='';
 $dataObj=[];
 if(count($selectBoxObj)>0){
    $customObjSelectBox.='<div class="container-fluid"><select id="e2_2" name="job_choosed_servces[]" multiple="multiple" style="width:100%" class="select2-multi-col">';
    foreach($selectBoxObj as $key => $subCategory){
      if(count($subCategory)>0){            
            $customObjSelectBox.='<optgroup label="'.$subCategory['name'].'">';
              for($i=0;$i<count($subCategory['obj']);$i++){
                $dataObj[$subCategory['name']][]=$subCategory['obj'][$i]['sub_service'];
                $selected="";
                if(count($subCategory)>0){
                  if(isset($setResponse[$subCategory['name']]) &&  is_array($setResponse[$subCategory['name']])){
                     if (in_array($subCategory['obj'][$i]['sub_service'], $setResponse[$subCategory['name']])) { $selected="selected";  }
                    }
                }
              $customObjSelectBox.='<option '.$selected.' value="'.$subCategory['name'].'_parent_'.$subCategory['obj'][$i]['sub_service'].'">'.$subCategory['obj'][$i]['sub_service'].'</option>';
            }
      }
    }
 
    $customObjSelectBox.='</optgroup>';  
    $customObjSelectBox.='</select></div>';
}
echo $customObjSelectBox;

}

//now we are saving the data
function facilities_save_meta_fields( $post_id ) {

  // verify nonce
  if (!isset($_POST['wpse_our_nonce']) || !wp_verify_nonce($_POST['wpse_our_nonce'], basename(__FILE__)))
      return 'nonce not verified';

  // check autosave
  if ( wp_is_post_autosave( $post_id ) )
      return 'autosave';

  //check post revision
  if ( wp_is_post_revision( $post_id ) )
      return 'revision';

  // check permissions
  if ( 'facilities' == $_POST['post_type'] ) {
      if ( ! current_user_can( 'edit_page', $post_id ) )
          return 'cannot edit page';
      } elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
          return 'cannot edit post';
  }


  //so our basic checking is done, now we can grab what we've passed from our newly created form
  $job_choosed_servces = $_POST['job_choosed_servces'];  
  //simply we have to save the data now
  update_post_meta($post_id, 'job_choosed_servces', $job_choosed_servces);

}
add_action( 'save_post', 'facilities_save_meta_fields', 10, 2  );
function my_acf_load_value( $value, $post_id, $field ) {

  if($field['type'] == 'repeater'){

  // echo "<pre>";print_r($field);
  // echo $value;
  // echo "===================";
  }
  
  // if( is_string($value) ) {
  //     $value = str_replace( 'Old Company Name', 'New Company Name',  $value );
  // }
  // return $value;
}

// Apply to all fields.
//add_filter('acf/load_value', 'my_acf_load_value', 10, 3);
 // Add field key of the repeater
 add_filter('acf/load_value/key=field_647eb02ca7cdb',  'afc_load_my_repeater_value', 10, 3);
 function afc_load_my_repeater_value($value, $post_id, $field) {

  //echo "<pre>";print_r($field);die;
       //Optional: Check for post_status otherwise published values will be changed.
  // if ( get_post_status( $post_id ) === 'auto-draft' ) {
           
            //Optional: Check for post_type.
    //  if( get_post_type( $post_id ) == 'cpt_type_1' ){
       //$value	= array();

               // Add field key for the field you would to put a default value (text field in this case)
      //  $value[] = array(
      //    'field_647eb02ca7cdc' => 'Tank:'
      //  );
      //  $value[] = array(
      //    'field_588a24c3cb782' => 'Dimensions:'
      //  );
      //  $value[] = array(
      //    'field_588a24c3cb782' => 'Weight:'
      //  );
      //  $value[] = array(
      //    'field_588a24c3cb782' => 'Base:'
      //  );
     //}

    //  if( get_post_type( $post_id ) == 'cpt_type_2'  ){
    //    $value	= array();
    //    $value[] = array(
    //      'field_588a24c3cb782' => 'Capacity:'
    //    );
    //    $value[] = array(
    //      'field_588a24c3cb782' => 'Load Rating:'
    //    );
    //    $value[] = array(
    //      'field_588a24c3cb782' => 'Dumping Methods:'
    //    );
    //    $value[] = array(
    //      'field_588a24c3cb782' => 'Color:'
    //    );
    //  }
   //}
   return $value;
 }
