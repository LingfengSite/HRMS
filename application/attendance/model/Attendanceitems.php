<?php
namespace app\attendance\model;
use think\Model;
class Attendanceitems extends Model
{
	protected $table = 'hrms_attendance_items';
	protected $readonly = 'id';
}