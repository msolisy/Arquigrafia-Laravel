<?php namespace lib\photoimport\import;

use Photo;
use Tag;
use User;
use lib\photoimport\ods\SheetReader;

class Importer {

  protected $tag;

  protected $photo;

  protected $reader;

  protected $logger;

  protected $ods;

  protected $user;

  public function __construct (Photo $photo, Tag $tag, SheetReader $sr, ImportLogger $logger) {
    $this->photo = $photo;
    $this->tag = $tag;
    $this->reader = $sr;
    $this->logger = $logger;
  }

  public function init() {
    $this->logger->init('ImportLogger', 'imports');
    $this->logger->logToFile(date('Y-m-d'));
  }

  public function setOds($ods) {
    $this->ods = $ods;
  }

  public function setUser($user) {
    $this->user = $user;
  }

  public function fire($job, $data) {
    $this->init();
    $ods = $data['ods'];
    $user = $data['user'];
    $this->setOds($ods);
    $this->setUser($user);
    if ( $this->checkUserExists() ) {
      $this->importFromFile();
    } else {
      $this->logUndefinedUser();
      return;
    }
  }

  public function checkUserExists() {
    return $this->user instanceof User;
  }

  public function importFromFile() {
    if ( ( $content = $this->getContent() ) == null ) { 
      return;
    }
    return $this->importContent($content);
  }

  public function importContent($content) {
    $photos = array();
    $ods_basepath = $this->ods->getBasePath();
    foreach ($content as $photo_data) {
      $photo_data['user_id'] = $this->user->id;
      $tag_data = array_pull( $photo_data, 'tags' );
      $new_photo = $this->import($ods_basepath, $photo_data, $tag_data);
      if ( $new_photo != null ) {
        $photos[] = $new_photo;
      }
    }
    return $photos;
  }

  public function getContent() {
    try {
      return $this->reader->read($this->ods);
    } catch (\Exception $e) {
      $this->logOdsReadingException($e, $this->ods);
      return null;
    }
  }

  public function import($basepath, $photo_data, $tag_data) {
    $photo = $this->importPhoto($photo_data, $basepath);
    if ( $photo != null) {
      $tags = $this->importTags($tag_data);
      $photo->syncTags($tags);
    }
    return $photo;
  }

  public function importPhoto($attributes, $basepath) {
    try {
       $photo = $this->photo->import($attributes, $basepath);
    } catch (\Exception $e) {
      $this->logPhotoException($e, $attributes['tombo']);
      return null;
    }
    $this->logImportedPhoto($photo);
    return $photo;
  }

  public function importTags($tag_data) {
    $tags = array();
    $raw_tags = $this->tag->transform($tag_data);
    foreach($raw_tags as $rt) {
      if ( ($tag = $this->getTag($rt)) != null ) {
        $tags[] = $tag;
        $this->logImportedTag($tag);
      }
    }
    return $tags;
  }

  public function getTag($tag_name) {
    try {
      return $this->tag->getOrCreate($tag_name);
    } catch (\Exception $e) {
      $this->logTagException($e, $tag_name);
      return null;
    }
  }

  public function logOdsReadingException($exception) {
    $message = "ods_reading_exception: {$this->ods->getPathname()}";
    $message .= ", exception_message: '" . $exception->getMessage() . "'";
    $this->logError($message);
  }

  public function logPhotoException($exception, $photo_tombo) {
    $message = "photo_import_exception: {$photo_tombo}";
    $message .= ", exception_message: '" . $exception->getMessage() . "'";
    $this->logError($message);
  }

  public function logTagException($exception, $tag_name) {
    $message = "tag_import_exception: {$tag_name}";
    $message .= ", exception_message: '{$exception->getMessage()}'";
    $this->logError($message);
  }

  public function logUndefinedUser() {
    $message = "undefined_user: {$this->user}";
    $message .= ",ods_file: {$this->ods->getPathname()}";
    $this->logger->addError($message);
  }

  public function logError($message) {
    $this->logger->addError($message);
    $this->ods->logError($message);
  }

  public function logInfo($message) {
    $this->logger->addInfo($message);
    $this->ods->logInfo($message);
  }

  public function logImportedPhoto($photo) {
    $message = "imported_photo: {$photo->tombo}";
    $this->logInfo($message);
  }

  public function logImportedTag($tag) {
    $message = "imported_tag: {$tag->name}";
    $this->logInfo($message);
  }

}