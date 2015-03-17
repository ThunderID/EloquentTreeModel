<?php namespace ThunderID\EloquentTreeModel;

use Validator, Exception;
use \Illuminate\Support\MessageBag;

class TreeModelObserver extends ModelObserver {
	
	/**
	 * 
	 *
	 * @return boolean|void
	 * @author 
	 **/
	public function updating($model)
	{
		if (!in_array('ITreeModel', class_implements($model)))
		{
			throw new Exception("Model must implements ITreeModel");
		}

		$this->generatePath($model);

		// ensure its ascendant is not one of its descendant
		if ($model->isDirty())
		{
			$old_path = $model->getOriginal('tree_path');
			if (str_is($old_path . $model->tree_path_delimiter . '*', $model->tree_path))
			{
				// generate message bag
				if (in_array('IHaveErrors', class_implements($model)))
				{
					$errors = new MessageBag(['Path' => 'Its new parent was one of its descendant']);
					$model->setErrors($errors);
				}
				// return false
				return false;
			}
		}
	}
	/**
	 * 
	 *
	 * @return boolean|void
	 * @author 
	 **/
	public function updated($model)
	{
		if (!in_array('ITreeModel', class_implements($model)))
		{
			throw new Exception("Model must implements ITreeModel");
		}

		// if current path is changed, move all of its old descendant
		if (!str_is($model->tree_path, $model->getOriginal('tree_path')))
		{
			$descendants = $model->TreePathStartWith($model->getOriginal('tree_path'))->get();
			foreach ($descendants as $descendant)
			{
				$descendant->tree_path = str_replace($model->getOriginal('tree_path'), $model->tree_path, $descendant->tree_path);
				$descendant->save();
			}
		}
	}

	/**
	 * 
	 *
	 * @return boolean|void
	 * @author 
	 **/
	public function saving($model)
	{
		if (!in_array('ITreeModel', class_implements($model)))
		{
			throw new Exception("Model must implements ITreeModel");
		}
		$this->generatePath($model);

		if ($model->isDirty())
		{
			$rules = [
						'name'			=> 'required',
						'tree_path'		=> 'required|unique:' . $model->getTable() . ',' . 'tree_path' . ',' . $model->getIdAttribute() . ',' . $model->getIdFieldName(),
					 ];

			$validator = Validator::make($model->toArray(), $rules);
			if ($validator->fails())
			{
				// generate message bag
				if (in_array('IHaveErrors', class_implements($model)))
				{
					$model->setErrors($validator->messages());
				}
				
				return false;
			}
		}
	}

	/**
	 * before deleting
	 *
	 * @return boolean|void
	 * @author 
	 **/
	public function deleting($model)
	{
		if (!in_array('ITreeModel', class_implements($model)))
		{
			throw new Exception("Model must implements ITreeModel");
		}

		if ($model->children->count())
		{
			if (in_array('IHaveErrors', class_implements($model)))
			{
				$model->setErrors(new MessageBag(['Deleting' => 'Unable to delete, as it has descendant(s)']));
			}
			return false;
		}
	}

	private function generatePath(ITreeModel $model)
	{
		if ($model->parent)
		{
			$model->tree_path = $model->parent->tree_path . $model->tree_path_delimiter . str_slug($model->name);
		}
		else
		{
			$model->tree_path = str_slug($model->name);
		}
	}
}