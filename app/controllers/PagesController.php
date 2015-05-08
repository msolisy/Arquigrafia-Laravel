<?php

class PagesController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Page Controller
	|--------------------------------------------------------------------------
	*/

	public function home()
	{
    $photos = Photo::orderByRaw("RAND()")->take(240)->get();
		return View::make('index', ['photos' => $photos]);
	}
  
  public function panel()
	{
    $photos = Photo::orderByRaw("RAND()")->take(240)->get();
		return View::make('api.panel', ['photos' => $photos]);
	}
	
	public function search()
	{
    //2015-05-06 msy begin, add param city
    $needle = Input::get("q");
    $txtcity = Input::get("city"); 

		if ($needle != "") {
      $tags = Tag::where('name', 'LIKE', '%' . $needle . '%')->get();
       Log::info("Logging info txtcity <".$txtcity.">");
       $photo = new Photo();
       if ($txtcity != "") {

        $allowed = "/[^a-z\\.\/\s]/i";
        $txtstreet=  preg_replace($allowed,"",$needle);
        $txtstreet = rtrim($txtstreet);         
          
        Log::info("Logging info txtcity <".$txtcity.">");            
          
        $query = Photo::where('id', '>', 0);  
        $query->where('city', 'LIKE', '%' . $txtcity . '%');
        $query->where('street', 'LIKE', '%' . $txtstreet . '%');
        $query->whereNull($photo->getQualifiedDeletedAtColumn()); 
        $photos = $query->get();
        
        $needle = $txtstreet;

       } else{           
           
           $query = Photo::where('id', '>', 0);        
           $query->where('name', 'LIKE', '%'. $needle .'%');  
           $query->orWhere('description', 'LIKE', '%'. $needle .'%');  
           $query->orWhere('imageAuthor', 'LIKE', '%' . $needle . '%');
           $query->orWhere('workAuthor', 'LIKE', '%'. $needle .'%');
           $query->orWhere('country', 'LIKE', '%'. $needle .'%');  
           $query->orWhere('state', 'LIKE', '%'. $needle .'%'); 
           $query->orWhere('city', 'LIKE', '%'. $needle .'%'); 
           $query->whereNull($photo->getQualifiedDeletedAtColumn()); 
           $photos = $query->get();

       } 
      //2015-05-06 msy end
      
      // se houver uma tag exatamente como a busca, pegar todas as fotos dessa tag e juntar no painel
      $tag = Tag::where('name', '=', $needle)->get();
      if ($tag->first()) {
        $byTag = $tag->first()->photos;
        $photos = $photos->merge($byTag);
      }
      // retorna resultado da busca
      return View::make('/search',['tags' => $tags, 'photos' => $photos, 'query'=>$needle, 'city'=>$txtcity]);
    } else {
      // busca vazia
      return View::make('/search',['tags' => [], 'photos' => [], 'query' => "", 'city'=>""]);
    }
	}
  
  public function advancedSearch()
	{ 
    //2015-05-06 msy begin, add workauthor
    $fields = array(
        'name',
        'description',
        'city',
        'state',
        'country',
        'workAuthor'
    );
    
    foreach($fields as $field) $$field = Input::get($field);
    
    if(empty($name) && empty($description) && empty($city) && empty($state) && empty($country) && empty($workAuthor)) {
       // busca vazia
       return View::make('/advanced-search',['tags' => [], 'photos' => [], 'query' => ""]);
    } else {
      
      $query = Photo::where('id', '>', 0);
      //
      if ($name != '') $query->where('name', 'LIKE', '%'. $name .'%');  
      if ($description != '') $query->where('description', 'LIKE', '%'. $description .'%');  
      if ($city != '') $query->where('city', 'LIKE', '%'. $city .'%');  
      if ($state != '') $query->where('state', 'LIKE', '%'. $state .'%'); 
      if ($country != '') $query->where('country', 'LIKE', '%'. $country .'%');  
      if ($workAuthor != '') $query->where('workAuthor', 'LIKE', '%'. $workAuthor .'%'); 
      $query->whereNull('deleted_at'); 
      $photos = $query->get();
      
    } //2015-05-06 msy end

    if($photos->count()) {
      // retorna resultado da busca
      return View::make('/advanced-search',['tags' => [], 'photos' => $photos]);
    } else {
      // busca sem resultados
      return View::make('/advanced-search',['tags' => [], 'photos' => []]);
    }
    
	}

}
