<?php namespace ThunderID\EloquentTreeModel;

use InvalidArgumentException;

trait TreeModelTrait {

	// ----------------------------------- RELATIONSHIP --------------------------------
	/**
	 * Parent relationship rule
	 *
	 * @return void
	 **/
	public function parent()
	{
		return $this->belongsTo( get_class($this), 'tree_parent_id');
	}

	/**
	 * Children relationship rule
	 *
	 * @return void
	 **/
	public function children()
	{
		return $this->HasMany( get_class($this), 'tree_parent_id');
	}


	// ----------------------------------- MUTATOR --------------------------------
	/**
	 * Set the name
	 * @param string $path
	 * @return void
	 **/
	public function setTreePathAttribute($value)
	{
		$this->attributes['tree_path'] = $value;
	}

	/**
	 * Set the name
	 * @param string $path
	 * @return void
	 **/
	public function setNameAttribute($value)
	{
		$this->attributes['name'] = $value;
	}

	// ----------------------------------- ACCESSOR --------------------------------
	/**
	 * get ascendant models
	 *
	 * @return void
	 **/
	public function getAscendantsAttribute()
	{
		$path = explode(($this->tree_path_delimiter ? $this->tree_path_delimiter : ";"), $this->attributes['tree_path']);
		$ascendant_path = [];
		foreach ($path as $x)
		{
			if (count($ascendant_path))
			{
				$ascendant_path[] = $ascendant_path[count($ascendant_path) - 1] . ($this->tree_path_delimiter ? $this->tree_path_delimiter : ";") . $x;
			}
			else
			{
				$ascendant_path[] = $x;
			}
		}
		return Static::whereIn('tree_path', $ascendant_path)->whereNotIn('tree_path', [$this->tree_path])->orderBy('tree_path', 'asc')->get();
	}

	/**
	 * get descendant models
	 *
	 * @return Models
	 **/
	public function getDescendantsAttribute()
	{
		return Static::where('tree_path', 'like', $this->tree_path . ($this->tree_path_delimiter ? $this->tree_path_delimiter : ";") . '%')->orderBy('tree_path', 'asc')->get();
	}

	
	// ----------------------------------- SCOPE --------------------------------
	/**
	 * create query finding exact tree path
	 *
	 * @return QueryBuilder
	 **/
	public function scopeTreePathIs($query, $value = null)
	{
		if (is_null($value))
		{
			return $query;
		}
		return $query->where('tree_path', 'like', $value);
	}

	/**
	 * create query for path not starting with value
	 *
	 * @return Models
	 **/
	public function scopeTreePathNotContains($query, $value = null)
	{
		if (is_null($value))
		{
			return $query;
		}
		return $query->where('tree_path', 'not regexp', '/' . preg_quote($value) . '/i');
	}

	/**
	 * create query finding model which tree path starts with
	 *
	 * @return QueryBuilder
	 **/
	public function scopeTreePathStartWith($query, $value = null)
	{
		if (is_null($value))
		{
			return $query;
		}
		return $query->where('tree_path', 'like', $value . '%');
	}

	/**
	 * search name
	 *
	 * @return void
	 **/
	public function scopeSearchName($query, $value = null)
	{
		if (!$value)
		{
			return $query;
		}

		return $query->where($this->getNameAttribute(),'like',$value);
	}

	// ----------------------------------- FUNCTIONS --------------------------------
}