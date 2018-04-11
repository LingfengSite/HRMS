<?php
namespace app\attendance\model;
use think\Model;
class Attendancerecords extends Model
{
	protected $table = 'hrms_attendance_records';
	protected $readonly = 'id';
}