<?php

// https://stackoverflow.com/questions/9896254/php-class-instance-to-json
class ProjectConst  implements JsonSerializable {
    public function jsonSerialize():mixed {
        return $this;
    }
	public $directory = "user_type";
	public $project_status = "project_status";
	public $project_type = "project_type";
	public $user = "user";
	public $userId = 1;
    public $employeeDefaultImage = 'images/user.png';
    public $csvUploadConfirmation = 'WARNING: The data shown in the preview will be uploaded as-is once you click OK.  If it is not 100% correct, DO NOT upload it.  Click Cancel and ask Support for help as it cannot be reversed.';

    public $noDetailsFoundMessage = "This item is not available. It may have been removed or access is not allowed.";

}