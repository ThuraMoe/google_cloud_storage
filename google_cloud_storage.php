<?php
	# This code is testing with cakephp

	/**
	 * Connect to google cloud storage
	 * @return storage, bucketName
	 */
	function connect_to_google_cloud_storage() {
		# Your Google Cloud Platform project ID
		$projectId = CloudStorageInfo::PROJECT_ID;
		$keyFilePath = CloudStorageInfo::KEY_FILE_PATH;

		# Instantiates a client
		$storage = new StorageClient([
		    'projectId' => $projectId,
		    'keyFilePath' => $keyFilePath
		]);

		# The name of the bucket
		$bucketName = CloudStorageInfo::BUCKET_NAME;

		return array($storage, $bucketName);
	}

	/**
	 * Upload a file.
	 *
	 * @param string $objectName the name of the object.
	 * @param string $source the path to the file to upload.
	 * @param string $folderStructure the path to save the file in cloud. 
	 *
	 * @return Psr\Http\Message\StreamInterface
	 */
	function upload_object_to_cloud($objectName, $source, $folderStructure) {
		/* 
			$objectName is the uploaded file name
			$objectName = $file[$i]['name'];

			$source is the uploaded tmp file name
			$source = $file[$i]['tmp_name'];

			Saving folder structure in cloud storage
			$folderStructure = folderName.'/'.year.'/'.month.'/';
		*/
		
		$cloud = parent::connect_to_google_cloud_storage();
		$storage = $cloud[0];
		$bucketName = $cloud[1];

		$file = fopen($source, 'r');
		$bucket = $storage->bucket($bucketName);
		try {
			$object = $bucket->upload($file, [
				'name' => $folderStructure.$objectName
			]);
		} catch (GoogleException $e) {
			CakeLog::write('debug', $e->getMessage().' in file '. __FILE__ . ' on line ' . __LINE__ . ' within the class ' . get_class());
		}
	}

	/**
	 * Download an object from Cloud Storage and save it as a local file.
	 *
	 * @param string $bucketName the name of your Google Cloud bucket.
	 * @param string $objectName the name of your Google Cloud object.
	 * @param string $destination the local destination to save the encrypted object.
	 *
	 * @return void
	 */
	function download_object_from_cloud() {

		# file path to download -> eg: 'picture/2019/06/flower.jpg'
		$url = 'picture/2019/06/flower.jpg';

	    try {
	    	$cloud = parent::connect_to_google_cloud_storage();
			$storage = $cloud[0];
			$bucketName = $cloud[1];
			$bucket = $storage->bucket($bucketName);
			$object = $bucket->object($url);
	    	$stream = $object->downloadAsStream();
			header('Content-disposition: attachment; filename*=UTF-8\'\''.rawurlencode($file_name));
			echo $stream->getContents();
			exit();
		} catch (GoogleException $e) {
			CakeLog::write('debug', $e->getMessage().' in file '. __FILE__ . ' on line ' . __LINE__ . ' within the class ' . get_class());
		}
	}

	/**
	 * Delete an object.
	 *
	 * @param string $bucketName the name of your Cloud Storage bucket.
	 * @param string $objectName the name of your Cloud Storage object.
	 * @param array $options
	 *
	 * @return void
	 */
	function delete_object_from_cloud() {
		# file path to delete -> eg: 'picture/2019/06/flower.jpg'
		$url = 'picture/2019/06/flower.jpg';
		try {
			$cloud = parent::connect_to_google_cloud_storage();
			$storage = $cloud[0];
			$bucketName = $cloud[1];
			$bucket = $storage->bucket($bucketName);
			$object = $bucket->object($url);
			if($object->exists()) {
				$object->delete();
			}
		} catch (GoogleException $e) {
			CakeLog::write('debug', $e->getMessage().' in file '. __FILE__ . ' on line ' . __LINE__ . ' within the class ' . get_class());
		}
	}



?>