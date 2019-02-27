<?php

namespace local_lti\resource_type;
use local_lti\provider\resource;

class book extends resource {

  /** @var int The page number of the resource to retrieve. */
  private $pagenum = null;

  public function get_book_id() {
    global $DB;

    // Retrieve the requested content ID.
    $content_id = $this->request->get_resource()->get_content_id();

    // Get the book object using the course module ID.
    $cm = get_coursemodule_from_id('book', $content_id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $book = $DB->get_record('book', array('id'=>$cm->instance), '*', MUST_EXIST);

    return $book->id;
  }

  public function get_lesson($pagenum = null) {
    global $DB;

    if (is_null($pagenum)) {
      $pagenum = $this->pagenum;
    }

    $lesson = $DB->get_record_sql('SELECT id, title, content
                                   FROM {book_chapters}
                                   WHERE bookid=?
                                   AND pagenum=?
                                   ORDER BY pagenum ASC', array($this->get_book_id(), $pagenum));

    return $lesson;
  }

  public function render() {
    global $PAGE;

    // Get the plugin renderer.
    $renderer = $PAGE->get_renderer('local_lti');

    try {

      $book_id = $this->get_book_id();

    } catch (\Exception $e) {
      throw new \Exception(get_string('error_book_id', 'local_lti'));
    }

    try {

      // Render book.
      $book = new \local_lti\output\book($book_id, $this->request->get_session_id(), $this->pagenum);
      echo $renderer->render($book);

    } catch (\Exception $e) {
      throw new \Exception(get_string('error_rendering_book', 'local_lti'));
    }
  }

  public function set_pagenum($pagenum) {
    $this->pagenum = $pagenum;
  }
}