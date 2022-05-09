<?php

namespace App\Actions\Photo\Strategies;

use App\Exceptions\MediaFileOperationException;
use App\Exceptions\ModelDBException;
use App\Image\MediaFile;
use App\Models\Photo;

/**
 * Adds a video as partner to an existing photo.
 *
 * Note the asymmetry to {@link AddPhotoPartnerStrategy}.
 * A video is always added to an already existing photo, and, in particular,
 * all EXIF data are taken from the that photo.
 * This allows to use {@link MediaFile} as the source of the video, because
 * no EXIF data needs to be extracted from the video.
 */
class AddVideoPartnerStrategy extends AddBaseStrategy
{
	protected MediaFile $videoSourceFile;

	public function __construct(AddStrategyParameters $parameters, MediaFile $videoSourceFile, Photo $existingPhoto)
	{
		parent::__construct($parameters, $existingPhoto);
		$this->videoSourceFile = $videoSourceFile;
	}

	/**
	 * @return Photo
	 *
	 * @throws MediaFileOperationException
	 * @throws ModelDBException
	 */
	public function do(): Photo
	{
		$photoFile = $this->photo->size_variants->getOriginal()->getFile();
		$photoPath = $photoFile->getRelativePath();
		$photoExt = $photoFile->getOriginalExtension();
		$videoExt = $this->videoSourceFile->getOriginalExtension();
		$videoPath = substr($photoPath, 0, -strlen($photoExt)) . $videoExt;
		$this->putSourceIntoFinalDestination($this->videoSourceFile, $videoPath);
		$this->photo->live_photo_short_path = $videoPath;
		$this->photo->save();

		return $this->photo;
	}

	protected function putSourceIntoFinalDestination(MediaFile $videoSourceFile, string $videoPath)
	{
		// TODO: Fill out
		// Attention: The previous (common parent) method was broken in various
		// ways, because it did not consider certain "edge" cases.
		//
		// If the source file is freshly uploaded (i.e. a native local file)
		// then the old method was mostly good.
		// But if the source file already exists in the final storage (i.e.
		// it is a Flysystem file), then we must properly _rename_ it.
		// Case 1: The file is located remotely. We don't want to stream
		// the existing file back to Lychee and then send it off again.
		// This is inefficient.
		// We want to rename it, in the remote location.
		// Case 2: The file is located on local storage and a symlink.
		// In this case we want to rename the symlink, but import the video
		// with its new name and remove the old symlink.
		// This would imply to accidentally resolve the symlink.
	}
}
