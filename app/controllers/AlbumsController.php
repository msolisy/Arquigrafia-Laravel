<?php

class AlbumsController extends \BaseController {

	public function __construct() {
		$this->beforeFilter('auth',
			array( 'except' => 'show' ));
		$this->beforeFilter('ajax',
			array( 'only' => array(
				'paginateAlbumPhotos',
				'updateInfo',
				'detachPhotos',
				'attachPhotos',
				'paginatePhotosNotInAlbum',
				'paginateCoverPhotos',
				'getList'
			)));
	}

	public function index() {
		
		if(Session::has('institutionId')){ 
      		$institution = Institution::find(Session::get('institutionId'));      		
      		$this->album = new Album();
      		$albums = $this->album->showAlbumsInstitutional($institution);       		

   		 }else{
   		 	$albums = Auth::user()->albums;
   		 }
   		 
		return View::make('albums.index')->with('albums', $albums);
	}

	public function create() {
		$url = URL::to('/albums/photos/add');
		$photos = Photo::paginateUserPhotos(Auth::user());
		$image = Session::has('image') ? Session::get('image') : null;
		if(Session::has('institutionId')){
      		$institution = Institution::find(Session::get('institutionId')); 
    	}else{
      		$institution = null;
    	}
		return View::make('albums.form')
			->with(['photos' => $photos,
				'url' => $url,
				'maxPage' => $photos->getLastPage(),
				'page' => 1,
				'type' => 'add',
				'image' => $image,
				'institution' => $institution
			]);
	}

	public function show($id) {
		$album = Album::find($id);
		if (is_null($album)) {
			return Redirect::to('/');
		}
		$photos = $album->photos;
		$user = $album->user;
		$other_albums = Album::withUser($user)->except($album)->get();
		return View::make('albums.show')
			->with([
				'photos' => $photos,
				'album' => $album,
				'user' => $user,
				'other_albums' => $other_albums
			]);
	}

	public function store() { 
		$photos = Input::get('photos_add');
		$cover = Photo::find((empty($photos) ? null : array_values($photos)[0]));
		$user = Auth::user();
		
		if(Session::has('institutionId')){
			$institutionData = Institution::find(Session::get('institutionId'));
			//$institution = $institutionData->id;
		}else{
			$institutionData = NULL;
		} 
		$album = Album::create([
			'title' => Input::get('title'),
			'description' => Input::get('description'),
			'user' => $user,
			'cover' => $cover,
			'institution' => $institutionData
		]);
		if ( $album->isValid() ) {
			if ( !empty($photos) ) {
				$album->attachPhotos($photos);
			}
			return Redirect::to('/albums/' . $album->id);
		}
		return Redirect::to('/albums/create')->withErrors($album->getErrors());
	}

	public function delete($id) {
		$album = Album::find($id);
		$user = Auth::user();
		if ( isset($album) && $user->equal($album->user) ) {
			$album->delete();
			Session::put('album.delete', 'Álbum ' . $album->title . ' deletado com sucesso.');
		}
		return Redirect::to('albums');
	}

	public function edit($id) {
		$user = Auth::user();
		$album = Album::find($id);
		if (is_null($album) || !$user->equal($album->user)) {
			return Redirect::to('/');
		}
		$album_photos = Photo::paginateAlbumPhotos($album);
		$other_photos_pagination = Photo::paginateUserPhotosNotInAlbum($user, $album);
		$other_photos = $other_photos_pagination['photos'];
		$other_photos_count = $other_photos_pagination['photos_count'];
		$maxPage = $other_photos->getLastPage();
		$rmMaxPage = $album_photos->getLastPage();
		$url = URL::to('/albums/' . $album->id . '/paginate/other/photos/');
		$rmUrl = URL::to('/albums/' . $album->id . '/paginate/photos');
		return View::make('albums.edition')
			->with(
				['album' => $album,
				'album_photos' => $album_photos,
				'other_photos' => $other_photos,
				'other_photos_count' => $other_photos_count,
				'page' => 1,
				'maxPage' => $maxPage,
				'rmMaxPage' => $rmMaxPage,
				'url' => $url,
				'rmUrl' => $rmUrl,
				'type' => 'rm',
				'photos' => null] );
	}

	public function removePhotos($id) {
		$album = Album::find($id);
		$photos = Input::except('_token');
		$album->detach($photos);
		return Redirect::to('albums/' . $id);
	}

	public function insertPhotos($id) {
		$album = Album::find($id);
		$photos = Input::except('_token');
		$album->attach($photos);
		return Redirect::to('albums/' . $id);
	}

	public function updateInfo($id) {
		$album = Album::find($id);
		if ( is_null($album) ) { return Redirect::to('/');	}
		$title = Input::get('title');
		$description = Input::get('description');
		$cover = Photo::find( Input::get('cover') );
		$album->updateInfo( $title, $description, $cover );
		if ( $album->save() ) {
			return Response::json("success");
		}
		return Response::json($album->getErrors());
	}

	public function paginateByUser() {
		$photos = Photo::paginateUserPhotos(Auth::user());
		$page = $photos->getCurrentPage();
		return Response::json(View::make('albums.includes.album-photos')
			->with(['photos' => $photos, 'page' => $page, 'type' => 'add'])
			->render());
	}

	public function paginateByAlbum($id) {
		$album = Album::find($id);
		$photos = Photo::paginateAlbumPhotos($album);
		$page = $photos->getCurrentPage();
		return Response::json(View::make('albums.includes.album-photos')
			->with(['photos' => $photos, 'page' => $page, 'type' => 'rm'])
			->render());
	}

	public function paginateByOtherPhotos($id) {
		$album = Album::find($id);
		$photos = Photo::paginateOtherPhotos(Auth::user(), $album->photos);
		$page = $photos->getCurrentPage();
		return Response::json(View::make('albums.includes.album-photos')
			->with(['photos' => $photos, 'page' => $page, 'type' => 'add'])
			->render());
	}

	public function paginateCoverPhotos($id) {
		$album = Album::find($id);
		$photos = Photo::paginateAlbumPhotos($album, 48);
		$photos_ids = [];
		foreach ($photos as $photo) {
			$photos_ids[] = $photo->id;
		}
		return $photos_ids;
	}

	public function getList($id) {
		$albums_with_photo = Photo::find($id)->albums; // albums que já têm essa foto
		$albums = Album::withUser( Auth::user() )->except($albums_with_photo)->get();
		return Response::json(View::make('albums.get-albums')
			->with(['albums' => $albums, 'photo_id' => $id])
			->render());
	}

	public function addPhotoToAlbums() {
		$albums_id = Input::get('albums');
		$photo = Photo::find(Input::get('_photo'));
		$albums = Album::findMany($albums_id);

		foreach ($albums as $album)
		{
			$album->photos()->sync(array($photo->id), false);
			if ( !isset($album->cover) ) {
				$album->cover()->associate($photo);
			}
		}
		if (Input::has('create_album')) {
			if ($albums->isEmpty()) {
				return Redirect::to('/albums/create')->with('image', $photo);
			} else {
				return Redirect::to('/albums/create')->with([
					'message' => '<strong>Imagem adicionada com sucesso ao(s) seu(s) álbum(ns)</strong>',
					'image' => $photo
				]);
			}
		}
		if ($albums->isEmpty()) {
			return Redirect::to('/photos/' . $photo->id);
		} else {
			return Redirect::to('/albums')->with('message', '<strong>Imagem adicionada com sucesso ao(s) seu(s) álbum(ns)</strong>');
		}
	}

	public function destroy($id) {
		$album = Album::find($id);
		$album->delete();
		return Redirect::to('/albums');
	}

	public function removePhotoFromAlbum($album_id, $photo_id) {
		$album = Album::find($album_id);
		$album->detachPhotos();
		return Redirect::to('/albums/' . $album->id);
	}

	public function detachPhotos($id) {
		try {
			$album = Album::find($id);
			$photos = Input::get('photos');
			$album->detachPhotos($photos);
		} catch (Exception $e) {
			return Response::json('failed');
		}
		return $this->paginateAlbumPhotos($id);
	}

	public function attachPhotos($id) {
		try { dd($id);
			$album = Album::find($id);
			$photos = Input::get('photos');
			$album->attachPhotos($photos);
		} catch (Exception $e) {
			return Response::json('failed');
		}
		return $this->paginatePhotosNotInAlbum($id);
	}

	public function paginateAlbumPhotos($id) {
		$album = Album::find($id);
		$query = Input::has('q') ? Input::get('q') : '';
		$pagination = Photo::paginateFromAlbumWithQuery($album, $query);
		return $this->paginationResponse($pagination, 'rm');
	}

	public function paginatePhotosNotInAlbum($id) {
		$album = Album::find($id);
		$user = Auth::user();
		if ( is_null($album) || !$user->equal($album->user) ) {
			return Response::json('failed');
		}
		$query = Input::has('q') ? Input::get('q') : '';
		$which_photos = Input::get('wp');
		$pagination = null;
		if (strcmp($which_photos, 'user') == 0) {
			$pagination = Photo::paginateUserPhotosNotInAlbum($user, $album, $query);
		} else {
			$pagination = Photo::paginateAllPhotosNotInAlbum($album, $query);
		}
		return $this->paginationResponse($pagination, 'add');
	}

	private function paginationResponse($pagination, $type) {
		$photos = $pagination['photos'];
		$count = $pagination['photos_count'];
		$page = $photos->getCurrentPage();
		$response = [];
		$response['content'] = View::make('albums.includes.album-photos-edit')
			->with(['photos' => $photos, 'page' => $page, 'type' => $type])
			->render();
		$response['maxPage'] = $photos->getLastPage();
		$response['empty'] = ($photos->count() == 0);
		$response['count'] = $count;
		return Response::json($response);
	}

}
