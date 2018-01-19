<?php
	/*
	*
	*  An API handle to query specific data keys from all of the
	*  currently existing slides.
	*
	*  GET parameters:
	*    Data can be requested by just assigning the key name
	*    a value such as 1 in the GET parameters.
	*
	*  Return value:
	*    A JSON encoded dictionary with the following keys:
	*      * data  = The requested data as nested dictionaries. **
	*      * error = An error code or API_E_OK on success. ***
	*
	*    **  (Only exists if the call was successful.)
	*    *** (The error codes are listed in api_errors.php.)
	*/

	require_once($_SERVER['DOCUMENT_ROOT'].'/common/php/util.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/api/api_util.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/api/api_errors.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/api/slide.php');

	header_plaintext();

	// Check that the requested keys are in SLIDE_REQ_KEYS.
	if (!count($_GET) || !array_is_subset(
		array_keys($_GET), SLIDE_REQ_KEYS)) {
		error_and_exit(API_E_INVALID_REQUEST);
	}

	$slides = get_slides_id_list();
	$ret = array('data' => array());
	$tmp_slide = new Slide();

	foreach($slides as $s) {
		$tmp_slide->clear();
		try {
			$tmp_slide->load($s);
		} catch (Exception $e) {
			error_and_exit(API_E_INTERNAL);
		}

		$ret['data'][$s] = array();
		foreach(array_keys($_GET) as $k) {
			$ret['data'][$s][$k] = $tmp_slide->get($k);
		}
	}
	$ret['error'] = API_E_OK;

	$ret_json = json_encode($ret);
	if ($ret === FALSE) {
		error_and_exit(API_E_INTERNAL);
	}
	echo $ret_json;
