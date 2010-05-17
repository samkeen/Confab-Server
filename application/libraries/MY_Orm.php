<?php defined('SYSPATH') or die('No direct script access.');

class Orm extends ORM_Core {

	/**
         *
         * Having to Override ORM_Core so it works as advertized
         * added the else clause to if ($status = $array->validate())
         * It really seems the Model should still have its fields populated if
         * Validation fails so we get behavior such as demontsrtated in
         * http://docs.kohanaphp.com/libraries/orm/examples#combining_orm_and_validation
         *
	 * Validates the current object. This method should generally be called
	 * via the model, after the $_POST Validation object has been created.
	 *
	 * @param   object   Validation array
	 * @return  boolean
	 */
	public function validate(Validation $array, $save = FALSE)
	{
		$safe_array = $array->safe_array();

		if ( ! $array->submitted())
		{
			foreach ($safe_array as $key => $value)
			{
				// Get the value from this object
				$value = $this->$key;

				if (is_object($value) AND $value instanceof ORM_Iterator)
				{
					// Convert the value to an array of primary keys
					$value = $value->primary_key_array();
				}

				// Pre-fill data
				$array[$key] = $value;
			}
		}

		// Validate the array
		if ($status = $array->validate())
		{
			// Grab only set fields (excludes missing data, unlike safe_array)
			$fields = $array->as_array();

			foreach ($fields as $key => $value)
			{
				if (isset($safe_array[$key]))
				{
					// Set new data, ignoring any missing fields or fields without rules
					$this->$key = $value;
				}
			}

			if ($save === TRUE OR is_string($save))
			{
				// Save this object
				$this->save();

				if (is_string($save))
				{
					// Redirect to the saved page
					url::redirect($save);
				}
			}
		} else {
			// Grab only set fields (excludes missing data, unlike safe_array)
			$fields = $array->as_array();

			foreach ($fields as $key => $value)
			{
				if (isset($safe_array[$key]))
				{
					// Set new data, ignoring any missing fields or fields without rules
					$this->$key = $value;
				}
			}
		}

		// Return validation status
		return $status;
	}
        
}