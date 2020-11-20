add_filter( 'wpcf7_validate', 'friendly_captcha_validate', 10, 2 );

function friendly_captcha_validate ( $result, $tags ) {
    // retrieve the captcha field
    $form  = WPCF7_Submission::get_instance();
    $solution = $form->get_posted_data('frc-captcha-solution');

    $response = httpPost( "https://friendlycaptcha.com/api/v1/siteverify", array("solution"=>$solution, "secret"=>"put-your-secret-in-here") );
    if ( empty($response) ) {
        $result->invalidate('your-message', 'capture validation empty, please refresh the form and try again');
        return $result;
    }
    $obj = json_decode($response);
    if ( $obj->{'success'} != 1 )
        $result->invalidate('your-message', 'capture validation failed, please refresh the form and try again');

    // return the filtered value
    return $result;
}

function httpPost($url, $data){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
