<?php
/*
    ===============================
    create shortcode
    ===============================
*/    
add_shortcode('book', 'wpbook1_shortcode');
function wpbook1_shortcode($attributes){
	//print_r($attributes);	
    echo "<br/><h5> ID: " . $attributes['id'] . "</h5>";	
    echo "<br/><h5> Author name: " . $attributes['author_name'] . "</h5>";
    echo "<br/><h5> Year: " . $attributes['year'] . "</h5>";
    echo "<br/><h5> Category: " . $attributes['category'] . "</h5>";
    echo "<br/><h5> Tag: " . $attributes['tag'] . "</h5>";
    echo "<br/><h5> Publisher:" . $attributes['publisher'] . "</h5><br/><br/>";
    //echo "<br/><h5> price: </h5>" . $attributes['price'] . "<br/><br/>";
}