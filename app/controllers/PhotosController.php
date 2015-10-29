<?php
//add
use lib\utils\ActionUser;
use lib\gamification\models\Badge;
use Carbon\Carbon;
use lib\date\Date;

class PhotosController extends \BaseController {

  protected $date;

  public function __construct(Date $date = null)
  {
    $this->beforeFilter('auth',
      array( 'except' => ['index','show'] ));
    $this->date = $date ?: new Date; 
  }

  public function index()
  {
    $photos = Photo::all();
    return View::make('/photos/index',['photos' => $photos]);
  }

  public function show($id)
  { 
    $photos = Photo::find($id);
    if ( !isset($photos) ) {
      return Redirect::to('/');
    }
    $user = Auth::user();
    $photo_owner = $photos->user;

    $photo_institution = $photos->institution;
    $tags = $photos->tags;
    $binomials = Binomial::all()->keyBy('id');
    $average = Evaluation::average($photos->id);
    $evaluations = null;
    $photoliked = null;
    $follow = true;
    $belongInstitution = false;
    $hasInstitution = false; 
    $institution = null;
    if (Auth::check()) {
      if(Session::has('institutionId')){
        $belongInstitution = Institution::belongInstitution($photos->id,Session::get('institutionId'));
        $hasInstitution = Institution::belongSomeInstitution($photos->id);
        $institution = Institution::find(Session::get('institutionId')); 
      } else{
        $hasInstitution = Institution::belongSomeInstitution($photos->id);
      }
      $evaluations =  Evaluation::where("user_id", $user->id)->where("photo_id", $id)->orderBy("binomial_id", "asc")->get();
      if ($user->following->contains($photo_owner->id)) {
        $follow = false;
      }
      $user_id = $user->id;
      $user_or_visitor = "user";
    } else {
        $user_or_visitor = "visitor";
        session_start();
        $user_id = session_id();
    }
    $source_page = Request::header('referer');
    ActionUser::printSelectPhoto($user_id, $id, $source_page, $user_or_visitor);

    $license = Photo::licensePhoto($photos);
    //$author = new Author();
    //$authorsPhoto = $author->filterAuthorByPhoto($photo);
    $authorsList = $photos->authors->lists('name');
    
    return View::make('/photos/show',
      ['photos' => $photos, 'owner' => $photo_owner, 'follow' => $follow, 'tags' => $tags,
      'commentsCount' => $photos->comments->count(),
      'commentsMessage' => static::createCommentsMessage($photos->comments->count()),
      'average' => $average, 'userEvaluations' => $evaluations, 'binomials' => $binomials,
      'architectureName' => Photo::composeArchitectureName($photos->name),
      'similarPhotos'=>Photo::photosWithSimilarEvaluation($average,$photos->id),
      'license' => $license,
      'belongInstitution' => $belongInstitution,
      'hasInstitution' => $hasInstitution,
      'ownerInstitution' => $photo_institution,
      'institution' => $institution,
      'authorsList' => $authorsList
    ]);
  }

  // upload form
  public function form()
  {
    if (Session::has('institutionId') ) {
      return Redirect::to('/');
    }
    $pageSource = Request::header('referer');
    if(empty($pageSource)) $pageSource = '';
    $tags = null;
    $work_authors = null;
    $centuryInput =  null;
    $decadeInput = null;
    $centuryImageInput = null;
    $decadeImageInput = null;
    $dates = false;
    $dateImage = false;

    if ( Session::has('tags') )
    {
      $tags = Session::pull('tags');
      $tags = explode(',', $tags);
    }

    if ( Session::has('work_authors') )
    {
      $work_authors = Session::pull('work_authors');
      $work_authors = explode(';', $work_authors);
    }

    if ( Session::has('centuryInput') ) {
       $centuryInput = Session::pull('centuryInput');
      //dd($century);
       $dates = true;
      }
    if ( Session::has('decadeInput') ){
       $decadeInput = Session::pull('decadeInput');
       $dates = true;
     }

     if ( Session::has('centuryImageInput') ) {
       $centuryImageInput = Session::pull('centuryImageInput');
      //dd($century);
       $dateImage = true;
      }
    if ( Session::has('decadeImageInput') ){
       $decadeImageInput = Session::pull('decadeImageInput');
       $dateImage = true;
     }

    $input['autoOpenModal'] = null;   

    return View::make('/photos/form')->with(['tags'=>$tags,'pageSource'=>$pageSource,       
      'user'=>Auth::user(),
      'centuryInput'=> $centuryInput,
      'decadeInput' =>  $decadeInput,
      'centuryImageInput'=> $centuryImageInput,
      'decadeImageInput' =>  $decadeImageInput,
      'autoOpenModal'=>$input['autoOpenModal'],
      'dates' => $dates,
      'dateImage' => $dateImage,
      'work_authors'=>$work_authors   
      ]);

  }





  public function formInstitutional()
  {
    if ( ! Session::has('institutionId') ) {
      return Redirect::to('/');
    }
    $user_id = Auth::user()->id;

    $institution = Institution::find(Session::get('institutionId'));
    $albumsInstitutional = Album::withInstitution($institution)->get();
    

    $pageSource = Request::header('referer');
    
    $tagsArea = null;
    $work_authors = null;
    $centuryInput =  null;
    $decadeInput = null;
    $centuryImageInput = null;
    $decadeImageInput = null;
    $workAuthorInput = null;
    $dates = false;
    $dateImage = false;

    if ( Session::has('tagsArea') )
    {  
      $tagsArea = Session::pull('tagsArea');
      $tagsArea = explode(',', $tagsArea); 
    }

    if ( Session::has('work_authors') )
    {
      $work_authors = Session::pull('work_authors');
      $work_authors = explode(';', $work_authors);
    }

   // if ( Session::has('workAuthorInput') )
     //  $workAuthorInput = Session::pull('workAuthorInput');

    if ( Session::has('centuryInput') ) {
       $centuryInput = Session::pull('centuryInput');
       $dates = true;
      //dd($century);
      }
    if ( Session::has('decadeInput') ){
       $decadeInput = Session::pull('decadeInput');
       $dates = true;
     }

    if ( Session::has('centuryImageInput') ) {
       $centuryImageInput = Session::pull('centuryImageInput');
        //echo "centuryImg";
       $dateImage = true;
      }
    if ( Session::has('decadeImageInput') ){
       $decadeImageInput = Session::pull('decadeImageInput');
       $dateImage = true;
     }
    
    $input['autoOpenModal'] = null;  
    /* */
    return View::make('/photos/form-institutional')->with(['tagsArea'=> $tagsArea,
      //'workAuthorInput' => $workAuthorInput, 
      'centuryInput'=> $centuryInput,
      'decadeInput' =>  $decadeInput,
      'centuryImageInput'=> $centuryImageInput,
      'decadeImageInput' =>  $decadeImageInput,
      'pageSource'=>$pageSource, 'user'=>Auth::user(), 
      'institution' => $institution,
      'albumsInstitutional'=>$albumsInstitutional,
      'autoOpenModal'=>$input['autoOpenModal'],
      'dates' => $dates,
      'dateImage' => $dateImage,
      'work_authors'=>$work_authors
      ]);
  }

  public static function formatTags($tagsType){
    $tagsType = array_map('trim', $tagsType);
    $tagsType = array_map('mb_strtolower', $tagsType); 
    $tagsType = array_unique($tagsType);    
    return $tagsType;
  }

  public static function saveTags($tags,$photo){
    
    try{
          foreach ($tags as $t) {               
              $tag = Tag::where('name', $t)
                     ->whereIn('type', array('Acervo','Livre'))->first();
              //dd($tag);
              if(is_null($tag)){
                $tag = new Tag();
                $tag->name = $t;
                $tag->type = 'Livre';
                $tag->save();
              }

              $photo->tags()->attach($tag->id);
             
              if($tag->count == null)
                  $tag->count = 0;
              $tag->count++;
              $tag->save();    
          }
          $saved = true;

          }catch(PDOException $e){
            Log::error("Logging exception, error to register tags");           
            $saved = false;
          }
      return $saved;  
  }


  public function saveFormInstitutional() {   
    Input::flashExcept('tagsArea', 'photo','work_authors'); 
    $input = Input::all();
     
    if (Input::has('tagsArea')){
      $input["tagsArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsArea"]); 
    }else{
      $input["tagsArea"] = '';
    } 

    if (Input::has('work_authors')){
        $input["work_authors"] = str_replace(array('","'), '";"', $input["work_authors"]);    
        $input["work_authors"] = str_replace(array( '"','[', ']'), '', $input["work_authors"]);    
    }else
        $input["work_authors"] = '';


   // if (Input::has('workAuthor')){  
   //    $input["workAuthor"] = str_replace(array('"'), '', $input["workAuthor"]);    
   // }  

    
      $rules = array(
      'support' => 'required',
      'tombo' => 'required',
      //'subject' => 'required', 
      'hygieneDate' => 'date_format:"d/m/Y"|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/',
      'backupDate' => 'date_format:"d/m/Y"|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/',
      'characterization' => 'required',
      
      'photo' => 'max:10240|required|mimes:jpeg,jpg,png,gif',
      'photo_name' => 'required',
      'tagsArea' => 'required',
      'country' => 'required',
      'imageAuthor' => 'required',
      'image_date' => 'date_format:d/m/Y|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/'
      //'authorization_checkbox' => 'required'
      
      );
    

    $validator = Validator::make($input, $rules);
    
    if($validator->fails()) { 
          $messages = $validator->messages();       
          return Redirect::to('/photos/uploadInstitutional')->with(['tagsArea' => $input['tagsArea'] ,
         // 'workAuthorInput'=>$input["workAuthor"],
          'decadeInput'=>$input["decade_select"],
          'centuryInput'=>$input["century"],
          'decadeImageInput'=>$input["decade_select_image"],
          'centuryImageInput'=>$input["century_image"],
          'work_authors'=>$input["work_authors"]   
          ])->withErrors($messages); 
    }else{       
      if(Input::hasFile('photo') and Input::file('photo')->isValid()) {
          $file = Input::file('photo');
          $photo = new Photo();
          $photo->nome_arquivo = $file->getClientOriginalName();

          $photo->support = $input["support"];
          $photo->tombo = $input["tombo"];
          $photo->subject = $input["subject"];
          if(!empty($input["hygieneDate"]))
              $photo->hygieneDate = $this->date->formatDate($input["hygieneDate"]);
          if(!empty($input["backupDate"]) )
              $photo->backupDate = $this->date->formatDate($input["backupDate"]);
          $photo->characterization = $input["characterization"];
          $photo->cataloguingTime = date('Y-m-d H:i:s');
          $photo->UserResponsible = $input["userResponsible"];          
          $photo->name = $input["photo_name"];
          if ( !empty($input["description"]) )
               $photo->description = $input["description"];
          //if ( !empty($input["workAuthor"]) )
          //$photo->workAuthor = $input["workAuthor"];
          
          if(!empty($input["workDate"])){             
             $photo->workdate = $input["workDate"];
             $photo->workDateType = "year";
          }elseif(!empty($input["decade_select"])){             
            $photo->workdate = $input["decade_select"];
            $photo->workDateType = "decade";
          }elseif (!empty($input["century"]) && $input["century"]!="NS") { 
            $photo->workdate = $input["century"];
            $photo->workDateType = "century";
          }else{ 
            $photo->workdate = NULL;
          }

          if(!empty($input["image_date"])){             
             $photo->dataCriacao = $this->date->formatDate($input["image_date"]);
             $photo->imageDateType = "date";
          }elseif(!empty($input["decade_select_image"])){             
            $photo->dataCriacao = $input["decade_select_image"];
            $photo->imageDateType = "decade";
          }elseif (!empty($input["century_image"]) && $input["century_image"]!="NS") { 
            $photo->dataCriacao = $input["century_image"];
            $photo->imageDateType = "century";
          }else{ 
            $photo->dataCriacao = NULL;
          }  

          $photo->country = $input["country"];
          if ( !empty($input["state"]) )
            $photo->state = $input["state"];
          if ( !empty($input["city"]) )
              $photo->city = $input["city"];
          if ( !empty($input["street"]) )
               $photo->street = $input["street"];
          if ( !empty($input["imageAuthor"]) )
              $photo->imageAuthor = $input["imageAuthor"];
          /*if ( !empty($input["imageDate"]) )
              $photo->dataCriacao = $input["imageDate"];*/
          if ( !empty($input["observation"]) )  
              $photo->observation = $input["observation"];
          if ( !empty($input["aditionalImageComments"]) )
              $photo->aditionalImageComments = $input["aditionalImageComments"];
          $photo->allowCommercialUses = $input["allowCommercialUses"];
          $photo->allowModifications = $input["allowModifications"];
          $photo->authorized = $input["authorized"];
          $photo->user_id = Auth::user()->id;
          $photo->dataUpload = date('Y-m-d H:i:s');
          $photo->institution_id = Session::get('institutionId');
          $photo->save();
          
          $ext = $file->getClientOriginalExtension();
          $photo->nome_arquivo = $photo->id.".".$ext;

          $photo->save();
          
          $tagsCopy = $input['tagsArea'];
          $tags = explode(',', $input['tagsArea']);
          
          if (!empty($tags)) {           
              $tags = static::formatTags($tags);              
              $tagsSaved = static::saveTags($tags,$photo);
              
              if(!$tagsSaved){ 
                  $photo->forceDelete();
                  $messages = array('tagsArea'=>array('Inserir pelo menos uma tag'));                  
                  return Redirect::to('/photos/uploadInstitutional')->with(['tagsArea' => $input['tagsArea']])->withErrors($messages);
              }

            }
            if ( !empty($input["new_album-name"]) ) {
              $album = Album::create([
                'title' => $input["new_album-name"],
                'description' => "",
                'user' => Auth::user(),
                'cover' => $photo,
                'institution' => Institution::find(Session::get('institutionId')),
              ]);
              if ( $album->isValid() ) {
                DB::insert('insert into album_elements (album_id, photo_id) values (?, ?)', array($album->id, $photo->id));
             }
            }
            elseif ( !empty($input["photo_album"]) ) {
              DB::insert('insert into album_elements (album_id, photo_id) values (?, ?)', array($input["photo_album"], $photo->id));
            }
           //add Album
           /* if (Input::has("albums_institution")) {              
                $album = new Album();
                $album->id = $input["albums_institution"];
                $album->attachPhotos($photo->id);               
            }*/
          $author = new Author();
          if (!empty($input["work_authors"])) {
              $author->saveAuthors($input["work_authors"],$photo);
          }
           
          $input['autoOpenModal'] = 'true';  
          $sourcePage = $input["pageSource"]; //get url of the source page through form
          //ActionUser::printUploadOrDownloadLog($photo->user_id, $photo->id, $sourcePage, "UploadInstitutional", "user");
          //ActionUser::printTags($photo->user_id, $photo->id, $tagsCopy, $sourcePage, "user", "Inseriu");


          if(array_key_exists('rotate', $input))
              $angle = (float)$input['rotate'];
          else
              $angle = 0;
          $image = Image::make(Input::file('photo'))->rotate($angle)->encode('jpg', 80); // todas começam com jpg quality 80
          $image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
          $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg'); // deveria ser 220h, mantem por já haver alguns arquivos assim.
          $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
          $file->move(storage_path().'/original-images', $photo->id."_original.".strtolower($ext)); // original

          $photo->saveMetadata(strtolower($ext));
          
          $input['photoId'] = $photo->id;
          $input['dates'] = true;
          $input['dateImage'] = true;
          //return Redirect::to("/photos/{$photo->id}");        
          return Redirect::back()->withInput($input);
        

      }else{
         $messages = $validator->messages();
          return Redirect::to('/photos/uploadInstitutional')->withErrors($messages);
      }  
    }
  }
  /**/
  public static function filterTagByType($photo,$tagType){
      $tagsArea = $photo->tags->toJson();
      $jsonTagsArea=json_decode($tagsArea);      
      $arrayTags = array_filter($jsonTagsArea,function($item) use ($tagType){
        return $item->type == $tagType;
      });
      $tagsTypeList = array(); 
      foreach ($arrayTags as $value) {
        array_push($tagsTypeList, $value->name);
      }
      return $tagsTypeList;
  } 

  public static function updateTags($newTags,$photo){
  
      $photo_tags = $photo->tags;
      $allTags = Tag::allTagsPhoto($photo->id); 
      //dd($allTags);
      foreach ($allTags as $tag){   
        $tag->count--;
        $tag->save();                
      }

      foreach ($allTags as $alltag) {
        $photo->tags()->detach($alltag->id);
      }

      try{    // dd($newTags); 
        foreach ($newTags as $t) {            
            $t = strtolower($t);           
             
            $tag = Tag::where('name', $t)
                     ->whereIn('type', array('Acervo','Livre'))->first();
             //        ->orWhere('type', 'Livre')
                    // ->first();
             //dd($tag);       
            if(is_null($tag)){
                $tag = new Tag();
                $tag->name = $t;
                $tag->type = 'Livre';
                $tag->save();
            }  

            
            
            $photo->tags()->attach($tag->id);

            if($tag->count == null)
                $tag->count = 0;
            $tag->count++;
            $tag->save(); 
        }
        $saved = true;

      }catch(PDOException $e){
          Log::error("Logging exception, error to register tags");           
          $saved = false;
      }
      return $saved;  
  }

  

  /* Edição do formulario institutional*/
  public function editFormInstitutional($id) {
    $photo = Photo::find($id);
    $logged_user = Auth::User();
    $institution_id = Session::get('institutionId');
    if ($logged_user == null || $institution_id == null) {
        return Redirect::to('/');
    } elseif ($institution_id == $photo->institution_id) {
        if (Session::has('tagsArea'))
        {
            $tagsArea = Session::pull('tagsArea');
            $tagsArea = explode(',', $tagsArea);
        } else {
            $tagsArea = $photo->tags->lists('name');
            //$tagsArea = static::filterTagByType($photo,"Acervo");      
        }
        /*if (Session::has('workAuthorInput')  )
        {  
            $workAuthorInput = Session::pull('workAuthorInput');      
        }else{
            $workAuthorInput = $photo->workAuthor;
        } */

        if ( Session::has('work_authors') )
        {
            $work_authors = Session::pull('work_authors');
            $work_authors = explode(';', $work_authors);
        }else{
            $work_authors = $photo->authors->lists('name');
        }

      $dateYear = "";
      $decadeInput = "";
      $centuryInput = "";
      $decadeImageInput = "";
      $centuryImageInput = "";
      $imageDateCreated = "";
      
      if(Session::has('workDate')){        
        $dateYear = Session::pull('workDate');
      }elseif($photo->workDateType == "year"){
        $dateYear = $photo->workdate;
      }/*elseif($photo->workDateType == NULL && $photo->workdate!= "" && DateTime::createFromFormat('Y-m-d', $photo->workdate) == true){
        $date = DateTime::createFromFormat("Y-m-d",$photo->workdate);
        $dateYear = $date->format("Y");
      }*/

      if(Session::has('imageDate')){        
        $imageDateCreated = Session::pull('imageDate');
      }elseif($photo->imageDateType == "date"){
        $imageDateCreated = $photo->dataCriacao;
      }/*elseif($photo->imageDateType == NULL && $photo->imageDateType!= "" && DateTime::createFromFormat('Y-m-d', $photo->dataCriacao) == true){
        $dateCreated = DateTime::createFromFormat("Y-m-d",$photo->dataCriacao);
        $imageDateCreated = $dateCreated->format("Y");
      }*/

      if(Session::has('decadeInput')){ 
         $decadeInput = Session::pull('decadeInput'); 
      }elseif ($photo->workDateType == "decade"){
          $decadeInput = $photo->workdate;
      }

      if(Session::has('centuryInput')){
         $centuryInput = Session::pull('centuryInput');
      }elseif($photo->workDateType == "century") {
         $centuryInput = $photo->workdate;
         //dd($centuryInput);
      }
       
      if(Session::has('decadeImageInput')){ 
         $decadeImageInput = Session::pull('decadeImageInput'); 
      }elseif ($photo->imageDateType == "decade"){
         $decadeImageInput = $photo->dataCriacao;
      }

      if(Session::has('centuryImageInput')){
         $centuryImageInput = Session::pull('centuryImageInput');
      }elseif($photo->imageDateType == "century") {
         $centuryImageInput = $photo->dataCriacao;
         //dd($centuryInput);
      }


        return View::make('photos.edit-institutional')
          ->with(['photo' => $photo, 'tagsArea' => $tagsArea,
          //'workAuthorInput' => $workAuthorInput,
          'dateYear' => $dateYear,
          'centuryInput'=> $centuryInput,
          'decadeInput' =>  $decadeInput,
          'centuryImageInput'=> $centuryImageInput,
          'decadeImageInput' =>  $decadeImageInput,
          'imageDateCreated' => $imageDateCreated,
          'user' => $logged_user,
          'institution' => $photo->institution,
          'work_authors' => $work_authors
          ] ); 
    }    
    return Redirect::to('/');
  }

  public function updateInstitutional($id){ 
      $photo = Photo::find($id); 
      Input::flashExcept('tagsArea','photo','workAuthor','decade_select'); 
      $input = Input::all(); 
      if(Input::has('tagsArea')){
        $input["tagsArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsArea"]);              
      }else{
        $input["tagsArea"] = '';      
      } 
      /*if(Input::has('workAuthor')){ 
        $input["workAuthor"] = str_replace(array('"'), '', $input["workAuthor"]);       
      }else{
        $input["workAuthor"] ="";
      } */
      if (Input::has('work_authors')){
          $input["work_authors"] = str_replace(array('","'), '";"', $input["work_authors"]);    
          $input["work_authors"] = str_replace(array( '"','[', ']'), '', $input["work_authors"]);    
      }else
          $input["work_authors"] = '';

      $workDate = "";
      $decadeInput = "";
      $centuryInput = "";
      $imageDateCreated = "";
      $decadeImageInput = "";
      $centuryImageInput = "";

      if(Input::has('workDate')){        
        $workDate = $input["workDate"];
      }elseif(Input::has('decade_select')){ 
         $decadeInput = $input["decade_select"];
      }elseif(Input::has('century')){
         $centuryInput = $input["century"];
      }

      if(Input::has('photo_imageDate')){        
        $imageDateCreated = $input["photo_imageDate"];
      }elseif(Input::has('decade_select_image')){ 
         $decadeImageInput = $input["decade_select_image"];
      }elseif(Input::has('century_image')){
         $centuryImageInput = $input["century_image"];
      }

      $rules = array(
      'support' => 'required',
      'tombo' => 'required',
      //'subject' => 'required',      
      'hygieneDate' => 'date_format:"d/m/Y"',
      'backupDate' => 'date_format:"d/m/Y"',
      'characterization' => 'required',
      
      'photo_name' => 'required',
      'tagsArea' => 'required',
      'country' => 'required',
      'imageAuthor' => 'required',
      'photo' => 'max:10240|mimes:jpeg,jpg,png,gif',           
      //'photo_workDate' => 'date_format:"d/m/Y"',
      'image_date' => 'date_format:d/m/Y|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/',
      );

      $validator = Validator::make($input, $rules);

      if($validator->fails()) { 
          $messages = $validator->messages();          
          return Redirect::to('/photos/'.$photo->id.'/editInstitutional')->with([
          'tagsArea' => $input['tagsArea'], 
          'decadeInput'=>$decadeInput,
          'centuryInput'=>$centuryInput,
          'workDate' => $workDate,
          'decadeImageInput' => $decadeImageInput,
          'centuryImageInput' => $centuryImageInput,          
          'imageDateCreated' => $imageDateCreated,
          //'workAuthorInput'=> $input["workAuthor"],
          'work_authors'=> $input["work_authors"] 
          ])->withErrors($messages); 
      }else{ 
          if(!empty($input["aditionalImageComments"]) )
              $photo->aditionalImageComments = $input["aditionalImageComments"];
          $photo->support = $input["support"];
          $photo->tombo = $input["tombo"];
          $photo->subject = $input["subject"];
          if ( !empty($input["hygieneDate"]) )
              $photo->hygieneDate = $this->date->formatDate($input["hygieneDate"]);
          else $photo->hygieneDate = null;

          if ( !empty($input["backupDate"]) )
              $photo->backupDate = $this->date->formatDate($input["backupDate"]);
          else   $photo->backupDate = null;
          $photo->characterization = $input["characterization"];
          $photo->cataloguingTime = date('Y-m-d H:i:s');
          $photo->UserResponsible = $input["userResponsible"];
          $photo->name = $input["photo_name"];

          if ( !empty($input["description"]) )
               $photo->description = $input["description"];
          else $photo->description = null;

          if ( !empty($input["workAuthor"]) )
               $photo->workAuthor = $input["workAuthor"];
          else $photo->workAuthor = null;
             
          /*if ( !empty($input["workDate"]) )
               $photo->workdate = $input["workDate"];
          else  $photo->workdate = null;*/

          if(!empty($input["workDate"])){             
             $photo->workdate = $input["workDate"];
             $photo->workDateType = "year";
          }elseif(!empty($input["decade_select"])){             
            $photo->workdate = $input["decade_select"];
            $photo->workDateType = "decade";
          }elseif (!empty($input["century"]) && $input["century"]!="NS") { 
            $photo->workdate = $input["century"];
            $photo->workDateType = "century";
          }else{ 
            $photo->workdate = NULL;
            $photo->workDateType = NULL;
          }          

          if(!empty($input["image_date"])){             
              $photo->dataCriacao = $this->date->formatDate($input["image_date"]);
              $photo->imageDateType = "date";
          }elseif(!empty($input["decade_select_image"])){             
              $photo->dataCriacao = $input["decade_select_image"];
              $photo->imageDateType = "decade";
          }elseif (!empty($input["century_image"]) && $input["century_image"]!="NS") { 
              $photo->dataCriacao = $input["century_image"];
              $photo->imageDateType = "century";
          }else{ 
              $photo->dataCriacao = NULL;
          }

             
          $photo->country = $input["country"];
          if ( !empty($input["state"]) )
               $photo->state = $input["state"];
          else $photo->state = null;   

          if ( !empty($input["city"]) )
               $photo->city = $input["city"];
          else $photo->city = null;   
          if ( !empty($input["street"]) )
               $photo->street = $input["street"];
          else $photo->street = null;
         
          if ( !empty($input["imageAuthor"]) )
               $photo->imageAuthor = $input["imageAuthor"];

         /* if ( !empty($input["imageDate"]) )
               $photo->dataCriacao = $input["imageDate"];
          else $photo->dataCriacao = null; */
             
          if ( !empty($input["observation"]) )  
               $photo->observation = $input["observation"];
          else $photo->observation = null;
          $photo->allowCommercialUses = $input["allowCommercialUses"];
          $photo->allowModifications = $input["allowModifications"];

          $photo->user_id = Auth::user()->id;
          $photo->dataUpload = date('Y-m-d H:i:s');
          $photo->institution_id = Session::get('institutionId');
          //dd(Input::hasFile('photo'));
          //dd(Input::file('photo')->isValid());
          if(Input::hasFile('photo') and Input::file('photo')->isValid()) {
              $file = Input::file('photo');
            //  dd($file);
              $ext = $file->getClientOriginalExtension();
              $photo->nome_arquivo = $photo->id.".".$ext;
          }
          $photo->touch();
          $photo->save();
          //tags
          $tagsCopy = $input['tagsArea'];
          $tags = explode(',', $input['tagsArea']);

          if(!empty($tags)) { 
              $tags = static::formatTags($tags);              
              $tagsSaved = static::updateTags($tags,$photo);

              if(!$tagsSaved){
                  $photo->forceDelete();
                  $messages = array('tagsArea'=>array('Inserir pelo menos uma tag') );
                
                  return Redirect::to('/photos/'.$photo->id.'/editInstitutional')->with([
                  'tagsArea' => $input['tagsArea'] ])->withErrors($messages);
              }
          }

           $author = new Author();
            if (!empty($input["work_authors"])) {
                $author->updateAuthors($input["work_authors"],$photo);
            }else{
                $author->deleteAuthorPhoto($photo);
            }
                    
          if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
              $image = Image::make(Input::file('photo'))->encode('jpg', 80); // todas começam com jpg quality 80
              $image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
              $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg'); // deveria ser 220h, mantem por já haver alguns arquivos assim.
              $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
              $file->move(storage_path().'/original-images', $photo->id."_original.".strtolower($ext)); // original
              $photo->saveMetadata(strtolower($ext));
          }
         // $source_page = Request::header('referer');
         // ActionUser::printTags($photo->user_id, $id, $tags_copy, $source_page, "user", "Editou");
          return Redirect::to("/photos/".$photo->id)->with('message', '<strong>Edição de informações da imagem</strong><br>Dados alterados com sucesso');
    }
  }  

  public function store() {


  Input::flashExcept('tags', 'photo','work_authors');

  $input = Input::all();

  if (Input::has('tags'))
    $input["tags"] = str_replace(array('\'', '"', '[', ']'), '', $input["tags"]);
  else
    $input["tags"] = '';

  if (Input::has('work_authors')){
    $input["work_authors"] = str_replace(array('","'), '";"', $input["work_authors"]);    
    $input["work_authors"] = str_replace(array( '"','[', ']'), '', $input["work_authors"]);    
  }else
    $input["work_authors"] = '';
 
  
    $rules = array(
      'photo_name' => 'required',
      'photo_imageAuthor' => 'required',
      'tags' => 'required',
      'photo_country' => 'required',  
      'photo_authorization_checkbox' => 'required',
      'photo' => 'max:10240|required|mimes:jpeg,jpg,png,gif',
      //'photo_workDate' => 'date_format:"d/m/Y"',      
      'photo_imageDate' => 'date_format:d/m/Y|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/'
    );

  $validator = Validator::make($input, $rules);

  if ($validator->fails()) {
      $messages = $validator->messages();
      //dd($messages);
    return Redirect::to('/photos/upload')->with(['tags' => $input['tags'],
      'decadeInput'=>$input["decade_select"],
      'centuryInput'=>$input["century"],
      'decadeImageInput'=>$input["decade_select_image"],
      'centuryImageInput'=>$input["century_image"] ,
      'work_authors'=>$input["work_authors"]     
      ])->withErrors($messages);
    } else {

    if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
      $file = Input::file('photo');
      $photo = new Photo();

      if ( !empty($input["photo_aditionalImageComments"]) )
        $photo->aditionalImageComments = $input["photo_aditionalImageComments"];
      $photo->allowCommercialUses = $input["photo_allowCommercialUses"];
      $photo->allowModifications = $input["photo_allowModifications"];
      $photo->city = $input["photo_city"];
      $photo->country = $input["photo_country"];
      if ( !empty($input["photo_description"]) )
        $photo->description = $input["photo_description"];
      if ( !empty($input["photo_district"]) )
        $photo->district = $input["photo_district"];
      if ( !empty($input["photo_imageAuthor"]) )
        $photo->imageAuthor = $input["photo_imageAuthor"];
      $photo->name = $input["photo_name"];
      $photo->state = $input["photo_state"];
      if ( !empty($input["photo_street"]) )
        $photo->street = $input["photo_street"];
      //if ( !empty($input["photo_workAuthor"]) )
      //  $photo->workAuthor = $input["photo_workAuthor"];
      
       if(!empty($input["workDate"])){             
             $photo->workdate = $input["workDate"];
             $photo->workDateType = "year";
       }elseif(!empty($input["decade_select"])){             
            $photo->workdate = $input["decade_select"];
            $photo->workDateType = "decade";
       }elseif (!empty($input["century"]) && $input["century"]!="NS") { 
            $photo->workdate = $input["century"];
            $photo->workDateType = "century";
       }else{ 
            $photo->workdate = NULL;
       }

      if(!empty($input["photo_imageDate"])){             
             $photo->dataCriacao = $this->date->formatDate($input["photo_imageDate"]);
             $photo->imageDateType = "date";
       }elseif(!empty($input["decade_select_image"])){             
            $photo->dataCriacao = $input["decade_select_image"];
            $photo->imageDateType = "decade";
       }elseif (!empty($input["century_image"]) && $input["century_image"]!="NS") { 
            $photo->dataCriacao = $input["century_image"];
            $photo->imageDateType = "century";
       }else{ 
            $photo->dataCriacao = NULL;
       }      
      
      $photo->nome_arquivo = $file->getClientOriginalName();

      $photo->user_id = Auth::user()->id;
      $photo->dataUpload = date('Y-m-d H:i:s');
      $photo->save();
      

      if ( !empty($input["new_album-name"]) ) {
        $album = Album::create([
          'title' => $input["new_album-name"],
          'description' => "",
          'user' => Auth::user(),
          'cover' => $photo,
        ]);
        if ( $album->isValid() ) {
            DB::insert('insert into album_elements (album_id, photo_id) values (?, ?)', array($album->id, $photo->id));
        }
      }
      elseif ( !empty($input["photo_album"]) ) {
        DB::insert('insert into album_elements (album_id, photo_id) values (?, ?)', array($input["photo_album"], $photo->id));
      }
      $ext = $file->getClientOriginalExtension();
      $photo->nome_arquivo = $photo->id.".".$ext;

      $photo->save();

      

      $tags_copy = $input['tags'];
      $tags = explode(',', $input['tags']);

      if (!empty($tags)) {
        $tags = array_map('trim', $tags);
        //$tags = array_map('strtolower', $tags);

        $tags = array_map('mb_strtolower', $tags); // com suporte para cadeias multibytes
        // tudo em minusculas, para remover redundancias, como Casa/casa/CASA
        $tags = array_unique($tags); //retira tags repetidas, se houver.
        foreach ($tags as $t) {
          $tag = Tag::where('name', $t)->first();

          if (is_null($tag)) {
            $tag = new Tag();
            $tag->name = $t;
            
            try {
              $tag->save();
            } catch (PDOException $e) {
                Log::error("Logging exception, error to register tags");
                $photo->forceDelete();
                //$messages = array('tags'=>array('invalido'));
                return Redirect::to('/photos/upload')->with(['tags' => $input['tags']]); //->withErrors($messages)
            }
            
          }
          $photo->tags()->attach($tag->id);
          if ($tag->count == null)
            $tag->count = 0;
          $tag->count++;
          $tag->save();
        }
      }

      $author = new Author();
      if (!empty($input["work_authors"])) {
          $author->saveAuthors($input["work_authors"],$photo);
      }
            
      $input['autoOpenModal'] = 'true';  
      $source_page = $input["pageSource"]; //get url of the source page through form
      ActionUser::printUploadOrDownloadLog($photo->user_id, $photo->id, $source_page, "Upload", "user");
      ActionUser::printTags($photo->user_id, $photo->id, $tags_copy, $source_page, "user", "Inseriu");


      if(array_key_exists('rotate', $input))
          $angle = (float)$input['rotate'];
      else
          $angle = 0;
      $image = Image::make(Input::file('photo'))->rotate($angle)->encode('jpg', 80); // todas começam com jpg quality 80
      $image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
      $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg'); // deveria ser 220h, mantem por já haver alguns arquivos assim.
      $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
      $image->fit(32,20)->save(public_path().'/arquigrafia-images/'.$photo->id.'_micro.jpg');
      $file->move(storage_path().'/original-images', $photo->id."_original.".strtolower($ext)); // original

      $photo->saveMetadata(strtolower($ext));
      $input['photoId'] = $photo->id;
      $input['dates'] = true;
      $input['dateImage'] = true;
      //return Redirect::to("/photos/{$photo->id}");
      return Redirect::back()->withInput($input);

    } else {
    $messages = $validator->messages();
      return Redirect::to('/photos/upload')->withErrors($messages);
    }
 }
}


  // ORIGINAL
  public function download($id)
  {
    if (Auth::check()) {
      $photo = Photo::find($id);
      if($photo->authorized) {
        $originalFileExtension = strtolower(substr(strrchr($photo->nome_arquivo, '.'), 1));
        $filename = $id . '_original.' . $originalFileExtension;
        $path = storage_path().'/original-images/'. $filename;

        if( File::exists($path) ) {

          $user_id = Auth::user()->id;
          $pageSource = Request::header('referer');
          ActionUser::printUploadOrDownloadLog($user_id, $id, $pageSource, "Download", "user");

          header('Content-Description: File Transfer');
          header("Content-Disposition: attachment; filename=\"". $filename ."\"");
          header('Content-Type: application/octet-stream');
          header("Content-Transfer-Encoding: binary");
          header("Cache-Control: public");
          readfile($path);

          exit;
        }
      }
      return "Arquivo original não encontrado.";
    } else {
      return "Você só pode fazer o download se estiver logado, caso tenha usuário e senha, faça novamente o login.";
    }
  }

  // COMMENT
  public function comment($id)
  {
    $input = Input::all();
    $rules = ['text' => 'required'];
    $validator = Validator::make($input, $rules);
    if ($validator->fails()) {
      $messages = $validator->messages();
      return Redirect::to("/photos/{$id}")->withErrors($messages);
    } else {
      $comment = ['text' => $input["text"], 'user_id' => Auth::user()->id];
      $comment = new Comment($comment);
      $photo = Photo::find($id);
      $photo->comments()->save($comment);

      $user = Auth::user();
      $source_page = Request::header('referer');
      ActionUser::printComment($user->id, $source_page, "Inseriu", $comment->id, $id, "user");
      
      /*Envio de notificação*/
      if ($user->id != $photo->user_id) {
        $user_note = User::find($photo->user_id);
        foreach ($user_note->notifications as $notification) {
        $info = $notification->render();
        if ($info[0] == "comment_posted" && $info[2] == $photo->id && $notification->read_at == null) {
          $note_id = $notification->notification_id;
          $note_user_id = $notification->id;
          $note = $notification;
        }
      }
      if (isset($note_id)) {
        $note_from_table = DB::table("notifications")->where("id","=", $note_id)->get();
        if (NotificationsController::isNotificationByUser($user->id, $note_from_table[0]->sender_id, $note_from_table[0]->data) == false) {
          $new_data = $note_from_table[0]->data . ":" . $user->id;
          DB::table("notifications")->where("id", "=", $note_id)->update(array("data" => $new_data, "created_at" => Carbon::now('America/Sao_Paulo')));
          $note->created_at = Carbon::now('America/Sao_Paulo');
          $note->save();  
        }
      }
      else Notification::create('comment_posted', $user, $comment, [$user_note], null);
      }
      return Redirect::to("/photos/{$id}");
    }

  }

  // EVALUATE
  public function saveEvaluation($id)
  {
	  if (Auth::check()) {
		  $evaluations =  Evaluation::where("user_id", Auth::id())->where("photo_id", $id)->get();
		  $input = Input::all();

		  if(Input::get('knownArchitecture') == true)
		  {
			  $knownArchitecture = Input::get('knownArchitecture');
		  }else{
			  $knownArchitecture = 'no';
		  }

		  if(Input::get('areArchitecture') == true)
		  {
			  $areArchitecture = Input::get('areArchitecture');
		  }else{
			  $areArchitecture = 'no';
		  }

		  $i = 0;
		  $user_id = Auth::user()->id;
		  $evaluation_string = "";
		  $evaluation_names = array(
			  "Vertical-Horizontal", 
			  "Opaca-Translúcida", 
			  "Assimétrica-Simétrica", 
			  "Simples-Complexa", 
			  "Externa-Interna", 
			  "Fechada-Aberta"
		  );

		  // Pegar do banco as possives métricas
		  $binomials = Binomial::all();

		  // Fazer um loop por cada e salvar como uma avaliação
		  if ($evaluations->isEmpty()) {
			  $insertion_edition = "Inseriu";
			  foreach ($binomials as $binomial) {
				  $bid = $binomial->id;
				  $newEvaluation = Evaluation::create([
					  'photo_id'=> $id,
					  'evaluationPosition'=> $input['value-'.$bid],
					  'binomial_id'=> $bid,
					  'user_id'=> $user_id,
					  'knownArchitecture'=>$knownArchitecture,
					  'areArchitecture'=>$areArchitecture
				  ]);

				  $evaluation_string = $evaluation_string . $evaluation_names[$i++] . ": " . $input['value-'.$bid] . ", ";
			  }
		  } else { 
			  $insertion_edition = "Editou";
			  foreach ($evaluations as $evaluation) {
				  $bid = $evaluation->binomial_id;
				  $evaluation->evaluationPosition = $input['value-'.$bid];
				  $evaluation->knownArchitecture = $knownArchitecture;
				  $evaluation->areArchitecture = $areArchitecture;
				  $evaluation->save();
				  $evaluation_string = $evaluation_string . $evaluation_names[$i++] . ": " . $input['value-'.$bid] . ", ";
			  }
		  }
		  $user_id = Auth::user()->id;
		  $source_page = Request::header('referer');
		  ActionUser::printEvaluation($user_id, $id, $source_page, "user", $insertion_edition, $evaluation_string);
		  return Redirect::to("/photos/{$id}/evaluate")->with('message', 
			  '<strong>Avaliação salva com sucesso</strong><br>Abaixo você pode visualizar a média atual de avaliações');
	  } else {
		  // avaliação sem login
		  return Redirect::to("/photos/{$id}")->with('message', 
			  '<strong>Erro na avaliação</strong><br>Faça login para poder avaliar');
	  }
  }

  // BATCH RESIZE
  public function batch()
  {
    $photos = Photo::all();
    foreach ($photos as $photo) {
      $path = public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg';
      // novo tamanho para home, o micro, para pré carregamento.
	  $new = public_path().'/arquigrafia-images/'.$photo->id.'_micro.jpg';
      if (is_file($path) && !is_file($new)) $image = Image::make($path)->fit(32,20)->save($new);
	  /*
	  $image = Image::make($path)->save(public_path().'/arquigrafia-images/'.$newid.'_view.jpg');
	  $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$newid.'_200h.jpg');
	  $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$newid.'_home.jpg');
	  */
    }
    return "OK.";
  }
  
  // BATCH REGENERATE
  public function batchRegenerate()
  {
    $photos = Photo::all();
    foreach ($photos as $photo) {
      $path = public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg';
	  $image = Image::make($path);
	  $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg');
	  $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
	  $image->fit(32,20)->save(public_path().'/arquigrafia-images/'.$photo->id.'_micro.jpg');
    }
    return "OK.";
  }

  public function evaluate($photoId ) { 
    if(isset($_SERVER['QUERY_STRING'])) parse_str($_SERVER['QUERY_STRING']);
    $user_id = Auth::user()->id;
    $source_page = Request::header('referer');
    if(isset($f)) {
    if($f == "sb") ActionUser::printEvaluationAccess($user_id, $photoId, $source_page, "user", "pelo botão abaixo da imagem");
    elseif($f == "c") ActionUser::printEvaluationAccess($user_id, $photoId, $source_page, "user", "pelo botão abaixo do gráfico");
    elseif($f == "g") ActionUser::printEvaluationAccess($user_id, $photoId, $source_page, "user", "pelo gráfico");
    }
    else ActionUser::printEvaluationAccess($user_id, $photoId, $source_page, "user", "diretamente");
    return static::getEvaluation($photoId, Auth::user()->id, true);
  }

// need to be modified
    private function checkCommentCount($number_comment, $badge_name){
      $user = Auth::user();
      if(($user->badges()->where('name', $badge_name)->first()) != null){
        return;
      }
      if (($user->comments->count()) == $number_comment){
        $badge=Badge::where('name', $badge_name)->first();
        $user->badges()->attach($badge);
      }
    }

	private function getEvaluation($photoId, $userId, $isOwner) {
		$photo = Photo::find($photoId);
		$binomials = Binomial::all()->keyBy('id');
		$average = Evaluation::average($photo->id);
		$evaluations = null;
		$averageAndEvaluations = null;
		$checkedKnowArchitecture = false;
		$checkedAreArchitecture = false;
		$user = null;
		$follow = true;

		if ($userId != null) {
			$user = User::find($userId);
			if (Auth::check()) {
				if (Auth::user()->following->contains($user->id))
					$follow = false;
				else
					$follow = true;
			}

			$averageAndEvaluations= Evaluation::averageAndUserEvaluation($photo->id,$userId);
			$evaluations =  Evaluation::where("user_id",
				$user->id)->where("photo_id", $photo->id)->orderBy("binomial_id", "asc")->get();
			$checkedKnowArchitecture= Evaluation::userKnowsArchitecture($photoId,$userId);
			$checkedAreArchitecture= Evaluation::userAreArchitecture($photoId,$userId);

		}

		return View::make('/photos/evaluate',
			[
				'photos' => $photo, 
				'owner' => $user, 
				'follow' => $follow, 
				'tags' => $photo->tags, 
				'commentsCount' => $photo->comments->count(), 
				'commentsMessage' => static::createCommentsMessage($photo->comments->count()),
				'average' => $average, 
				'userEvaluations' => $evaluations,
				'userEvaluationsChart' => $averageAndEvaluations, 
				'binomials' => $binomials,
				'architectureName' => Photo::composeArchitectureName($photo->name),
				'similarPhotos'=>Photo::photosWithSimilarEvaluation($average,$photo->id),
				'isOwner' => $isOwner,
				'checkedKnowArchitecture' => $checkedKnowArchitecture,
				'checkedAreArchitecture' => $checkedAreArchitecture
			]);
	}


	public function edit($id) {
    if (Session::has('institutionId') ) {
      return Redirect::to('/');
    }
		$photo = Photo::find($id);
		$logged_user = Auth::User();
		if ($logged_user == null) {
			return Redirect::action('PagesController@home');
		}
		elseif ($logged_user->id == $photo->user_id) {
			if (Session::has('tags'))
			{
				$tags = Session::pull('tags');
				$tags = explode(',', $tags);
			} else {
				$tags = $photo->tags->lists('name');
			}

      if( Session::has('work_authors'))
      {
        $work_authors = Session::pull('work_authors');
        $work_authors = explode(';', $work_authors);
      } else{
        $work_authors = $photo->authors->lists('name');
      }

      $dateYear = "";
      $decadeInput = "";
      $centuryInput = "";
      $decadeImageInput = "";
      $centuryImageInput = "";
            
      if(Session::has('workDate')){     
        $dateYear = Session::pull('workDate');
      }elseif($photo->workDateType == "year"){
        $dateYear = $photo->workdate;
      }/*elseif($photo->workDateType == NULL && $photo->workdate!= "" && DateTime::createFromFormat('Y-m-d', $photo->workdate) == true){
              $date = DateTime::createFromFormat("Y-m-d",$photo->workdate);
              $dateYear = $date->format("Y");
      }*/

      if(Session::has('decadeInput')){ 
         $decadeInput = Session::pull('decadeInput'); 
      }elseif ($photo->workDateType == "decade"){
          $decadeInput = $photo->workdate;
      }

      if(Session::has('centuryInput')){
         $centuryInput = Session::pull('centuryInput');
      }elseif($photo->workDateType == "century") {
         $centuryInput = $photo->workdate;
         //dd($centuryInput);
      }

      if(Session::has('decadeImageInput')){ 
         $decadeImageInput = Session::pull('decadeImageInput'); 
      }elseif ($photo->imageDateType == "decade"){
         $decadeImageInput = $photo->dataCriacao;
      }

      if(Session::has('centuryImageInput')){
         $centuryImageInput = Session::pull('centuryImageInput');
      }elseif($photo->imageDateType == "century") {
         $centuryImageInput = $photo->dataCriacao;
         //dd($centuryInput);
      }
      

			return View::make('photos.edit')
				->with(['photo' => $photo, 'tags' => $tags,
            'dateYear' => $dateYear,
            'centuryInput'=> $centuryInput,
            'decadeInput' =>  $decadeInput,
            'centuryImageInput'=> $centuryImageInput,
            'decadeImageInput' =>  $decadeImageInput,
            'work_authors' => $work_authors
          ] );
		}
		return Redirect::action('PagesController@home');
	}

  public function update($id) {
    $photo = Photo::find($id);
     Input::flashExcept('tags', 'photo');
     $input = Input::all();

    if (Input::has('tags'))
      $input["tags"] = str_replace(array('\'', '"', '[', ']'), '', $input["tags"]);
    else
      $input["tags"] = '';

    if (Input::has('work_authors')){
      $input["work_authors"] = str_replace(array('","'), '";"', $input["work_authors"]);    
      $input["work_authors"] = str_replace(array( '"','[', ']'), '', $input["work_authors"]);    
    }else
      $input["work_authors"] = '';
    
      $workDate = "";
      $decadeInput = "";
      $centuryInput = "";
      $imageDateCreated = "";
      $decadeImageInput = "";
      $centuryImageInput = "";

      if(Input::has('photo_imageDate')){        
        $imageDateCreated = $input["photo_imageDate"];
      }elseif(Input::has('decade_select_image')){ 
         $decadeImageInput = $input["decade_select_image"];
      }elseif(Input::has('century_image')){
         $centuryImageInput = $input["century_image"];
      }

      if(Input::has('workDate')){        
        $workDate = $input["workDate"];
      }elseif(Input::has('decade_select')){ 
         $decadeInput = $input["decade_select"];
      }elseif(Input::has('century')){
         $centuryInput = $input["century"];
      }

    $rules = array(
        'photo_name' => 'required',
        'photo_imageAuthor' => 'required',
        'tags' => 'required',
        'photo_country' => 'required',
        //'photo_workDate' => 'date_format:"d/m/Y"',
        'photo_imageDate' => 'date_format:d/m/Y|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/',
        'photo' => 'max:10240|mimes:jpeg,jpg,png,gif'

    );

  $validator = Validator::make($input, $rules);

  if ($validator->fails()) {
      
      $messages = $validator->messages();
      return Redirect::to('/photos/' . $photo->id . '/edit')->with(['tags' => $input['tags'],
        'decadeInput' => $decadeInput,
        'centuryInput' => $centuryInput,   
        'workDate' => $workDate,
        'decadeImageInput'=>$decadeImageInput,
        'centuryImageInput'=>$centuryImageInput,  
        'imageDateCreated' => $imageDateCreated,
        'work_authors'=>$input["work_authors"] 
        ])->withErrors($messages);

    } else {
      if ( !empty($input["photo_aditionalImageComments"]) )
        $photo->aditionalImageComments = $input["photo_aditionalImageComments"];
      $photo->allowCommercialUses = $input["photo_allowCommercialUses"];
      $photo->allowModifications = $input["photo_allowModifications"];
      $photo->city = $input["photo_city"];
      $photo->country = $input["photo_country"];
      $photo->description = $input["photo_description"];
      $photo->district = $input["photo_district"];
      $photo->imageAuthor = $input["photo_imageAuthor"];
      $photo->name = $input["photo_name"];
      $photo->state = $input["photo_state"];
      $photo->street = $input["photo_street"];
      //$photo->workAuthor = $input["photo_workAuthor"];
      
      
      if(!empty($input["workDate"])){             
             $photo->workdate = $input["workDate"];
             $photo->workDateType = "year";
          }elseif(!empty($input["decade_select"])){             
            $photo->workdate = $input["decade_select"];
            $photo->workDateType = "decade";
          }elseif (!empty($input["century"]) && $input["century"]!="NS") { 
            $photo->workdate = $input["century"];
            $photo->workDateType = "century";
          }else{ 
            $photo->workdate = NULL;
            $photo->workDateType = NULL;
          }

      if(!empty($input["photo_imageDate"])){             
             $photo->dataCriacao = $this->date->formatDate($input["photo_imageDate"]);
             $photo->imageDateType = "date";
       }elseif(!empty($input["decade_select_image"])){             
            $photo->dataCriacao = $input["decade_select_image"];
            $photo->imageDateType = "decade";
       }elseif (!empty($input["century_image"]) && $input["century_image"]!="NS") { 
            $photo->dataCriacao = $input["century_image"];
            $photo->imageDateType = "century";
       }else{ 
            $photo->dataCriacao = NULL;
       }  


      if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
        $file = Input::file('photo');
        $ext = $file->getClientOriginalExtension();
        $photo->nome_arquivo = $photo->id.".".$ext;
      }
      //update o field update_at
      $photo->touch();
      $photo->save();

      $tags_copy = $input['tags'];
      $tags = explode(',', $input['tags']);

      if (!empty($tags)) {
        $tags = array_map('trim', $tags);
        $tags = array_map('mb_strtolower', $tags);

        $tags_id = [];
        $photo_tags = $photo->tags;
        // tudo em minusculas, para remover redundancias, como Casa/casa/CASA
        $tags = array_unique($tags); //retira tags repetidas, se houver.

        foreach ($tags as $t) {

          $tag = Tag::where('name', $t)->first();

          if (is_null($tag)) {
            $tag = new Tag();
            $tag->name = $t;

            try{
              $tag->save();
            }catch(PDOException $e) {
              Log::error("Logging exception, error to edit tags 1");

              $messages = array('tags'=>array('Erro nos tags'));
              return Redirect::to("/photos/{$photo->id}/edit")->with(['tags' => $input['tags']])->withErrors($messages);

            }
          }
          if ( !$photo_tags->contains($tag) )
          {
            if ($tag->count == null) $tag->count = 0;
            $tag->count++;
            $photo->tags()->attach($tag->id);
            try{
              $tag->save();
            }catch(PDOException $e) {
              Log::error("Logging exception, error to edit tags 2");
              $messages = array('tags'=>array('Erro nos tags'));
              return Redirect::to("/photos/{$photo->id}/edit")->with(['tags' => $input['tags']])->withErrors($messages);
            }

          }
          array_push($tags_id, $tag->id);
        }


        foreach($photo_tags as $tag)
        {
          if (!in_array($tag->id, $tags_id))
          {
            $tag->count--;
            $photo->tags()->detach($tag->id);
            try{
              $tag->save();
            }catch(PDOException $e) {
              Log::error("Logging exception, error to edit tags 3");
              $messages = array('tags'=>array('Erro nos tags'));
              return Redirect::to("/photos/{$photo->id}/edit")->with(['tags' => $input['tags']])->withErrors($messages);
            }
          }
        }

      }

      $author = new Author();
      if (!empty($input["work_authors"])) {
          $author->updateAuthors($input["work_authors"],$photo);
      }else{

          $author->deleteAuthorPhoto($photo);
      }

      if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
        $image = Image::make(Input::file('photo'))->encode('jpg', 80); // todas começam com jpg quality 80
        $image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
        $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg'); // deveria ser 220h, mantem por já haver alguns arquivos assim.
        $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
        $file->move(storage_path().'/original-images', $photo->id."_original.".strtolower($ext)); // original
        $photo->saveMetadata(strtolower($ext));
      }
      $source_page = Request::header('referer');
      ActionUser::printTags($photo->user_id, $id, $tags_copy, $source_page, "user", "Editou");
      return Redirect::to("/photos/{$photo->id}")->with('message', '<strong>Edição de informações da imagem</strong><br>Dados alterados com sucesso');

  }
}

  public function destroy($id) {
    $photo = Photo::find($id);
    foreach($photo->tags as $tag) {
      $tag->count = $tag->count - 1;
      $tag->save();
    }
    DB::table('tag_assignments')->where('photo_id', '=', $photo->id)->delete();
    $photo->delete();
    return Redirect::to('/users/' . $photo->user_id);
  }

  public function viewEvaluation($photoId, $userId ) {
    return static::getEvaluation($photoId, $userId, false);
  }

  public function showSimilarAverage($photoId) {
    return static::getEvaluation($photoId, null, false);
  }

  public function createCommentsMessage($commentsCount){
    $commentsMessage = '';
    if($commentsCount == 0)
      $commentsMessage = 'Ninguém comentou ainda esta imagem';
    else if($commentsCount == 1)
      $commentsMessage = 'Existe ' . $commentsCount . ' comentário sobre esta imagem';
    else
      $commentsMessage = 'Existem '. $commentsCount . ' comentários sobre esta imagem';
    return $commentsMessage;
  }

}
