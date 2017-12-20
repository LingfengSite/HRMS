/*
	function:网页数据初始化
	create:2017/12/15 15:28
	Author:Lingfeng Wei
*/
$(document).ready(function(){
	//服务量列表操作
	var serviceT = new Vue({
		el:'#service-list',
		data:{
		scoreList:[],//服务量列表
		programList:[],//项目服务内容列表
		
		},
		created:function(){
			$.get("/service/Getdata/return_program_project/",function(data){
				obj=$.parseJSON(data);
				serviceT.programList=obj;
			}),
			$.get("/service/index/score_list/",function(data){
				obj=$.parseJSON(data);
				if(obj.code=="200"){
					serviceT.scoreList=obj.data;
				}
			})
		},
		filters:{
			//项目及服务内容编码转换
			setName:function(value,program){
				var str="";
				obj=serviceT.programList
				if(program!=undefined){
					str=obj[program]["project"][value];
				}
				else{
					str=obj[value]["name"];
				}
					return str;
			}
		}
	});
	//课程列表操作
	var programT = new Vue({	
		el:'#ServiceBox',
		data:{
			programList:[],//项目列表
			selected:0,//select框默认1
			serviceList:[],//服务列表
			teacherList:[],
		},
		created:function(){
		//发送get请求，获得项目列表及服务列表，默认获得第一个项目的服务列表内容
			$.get("/service/Getdata/return_program_project/",function(data){
				obj=$.parseJSON(data);
				programT.programList=obj;
				programT.serviceList=obj[1].project;
			}),
			$.get("/service/Getdata/json_return_users",function(data){
				obj=$.parseJSON(data);
				programT.teacherList=obj;
			})
		},
		watch: {//监听selected变化，select联动
			selected:function(val){
				obj=this.programList[val+1].project;
				this.serviceList=obj;
			},
		}
	});
	var teacherT= new Vue({
		el:'#teacher',
		data:{
			teacherList:[],//教师列表
			searchData:[],
			searchText:"",
			addData:[],
			idx:0,
		},
		created:function(){
			$.get("/service/Getdata/json_return_users",function(data){
				obj=$.parseJSON(data);
				teacherT.teacherList=obj;
			})
		},
		watch:{
			searchText: function(items){
				arr={};
				if(teacherT.searchText!=""){
					j=0;
					for(i=0;i<teacherT.teacherList.length;i++){
						if(teacherT.teacherList[i].username.isLike(teacherT.searchText)){
							arr[j]=teacherT.teacherList[i];
							j++;
						}
					}
					teacherT.searchData=arr;
				}
			},
			idx:function(){
				
			}
		},
		methods:{
			addBox:function(id,username){
				$("#addText").val(this.idx);
				if(this.idx>0){
					var t=0;
					for(j=0;j<this.addData.length;j++){
						if(this.addData[j].userid===id){
							t=1;	
						}
					}
					if(t==0){
						this.addData[this.idx]={"userid":id,"username":username};
						this.idx++;
					}
				}
				else{
					this.addData[this.idx]={"userid":id,"username":username};
					this.idx++;
				}
			},
			deleteBox:function(id,username){
				for(j=0;j<this.idx;j++){
					if(this.addData[j].userid==id){
						this.addData.splice(j,1);
						this.idx--;
					}
				}
				
				$("#addText").val(this.idx);
			},
			saveTeacher:function(){
				/*programT.teacherList=this.addData;
				programT.teacherTg=1;
				$("#teacherTg").val(1);*/
				//$("#teacherAdd").val(1);
			}
		}
	});
	$(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii'});//时间框初始化
});
var Service = function () { }
Service.prototype = {
	ModifyService:function(id,teacher,program,service,serviceTime,duration,schoolYear){
		$("#serviceId").val(id);
		$("#teacherOption")[0].innerHTML=teacher;
		$("#programOption select")[0].selectedIndex=program-1;
		$("#programOption select")[1].selectedIndex=service-1;
		$("#serviceTime").val(serviceTime);
		$("#duration").val(duration);
		$("#schoolYear span")[0].innerHTML=schoolYear;
		$("#schoolYear select").hide();
	},
	AddServiceInit:function(){
		$("#serviceId").val();
		//$("#teacherOption")[0].innerHTML='选择教师：<a href="#" data-toggle="modal" data-target="#teacher"><i class="zmdi zmdi-plus"></i></a>';
		$("#programOption select")[0].selectedIndex=0;
		$("#programOption select")[1].selectedIndex=0;
		$("#serviceTime").val("");
		$("#duration").val("");
		$("#schoolYear span")[0].innerHTML="";
		$("#schoolYear select").show();
	},
	saveService:function(){
		if($("#serviceId").val()!=""){
			var serviceId=$("#serviceId").val();
		//	var teacherId=$("#teacherOption select")[0].value;
			var project=$("#programOption select")[0].value;
			var service=$("#programOption select")[1].value;
			var serviceTime=$("#serviceTime").val();
			var duration=$("#duration").val();
			$.ajax({
				url:"/service/index/revise_single",
				type:"post",
				data:{"id":serviceId,"user_id":teacherId,"program":project,"project":service,"duration":duration,"date":serviceTime},
				success:function(data){		
					data=$.parseJSON(data);
				}
			})
		}
		else{
			var teacherId=$("#teacherOption select")[0].value;
			var project=$("#programOption select")[0].value;
			var service=$("#programOption select")[1].value;
			var serviceTime=$("#serviceTime").val();
			serviceTime=Date.parse(new Date(serviceTime))/1000;
			var duration=$("#duration").val();
			var school_team=$("#schoolYear select")[0].value;
			$.ajax({
				url:"/service/index/add",
				type:"post",
				data:{"user_id":teacherId,"program":project,"project":service,"school_term":school_team,"duration":duration,"date":serviceTime},
				success:function(data){		
					data=$.parseJSON(data);
				}
			})
		}
	}
}
/*
	function:修改框弹出内容初始化
	@id					服务id
	@teacher			教师名称
	@program			项目
	@service			服务内容
	@serviceTime		服务日期
	@duration			时长
	@shoolYear			学年
	create:2017/12/15 15:28
	Author:Lingfeng Wei

*/
ModifyService=function(id,teacher,program,service,serviceTime,duration,schoolYear){
	$("#serviceId").val(id);
	$("#teacherOption")[0].innerHTML=teacher;
	$("#programOption select")[0].selectedIndex=program-1;
	$("#programOption select")[1].selectedIndex=service-1;
	$("#serviceTime").val(serviceTime);
	$("#duration").val(duration);
	$("#schoolYear span")[0].innerHTML=schoolYear;
	$("#schoolYear select").hide();
}
/*
	function:添加服务量初始化
	create:2017/12/15 15:28
	Author:Lingfeng Wei

*/
AddServiceInit=function(){
	$("#serviceId").val();
	//$("#teacherOption")[0].innerHTML='选择教师：<a href="#" data-toggle="modal" data-target="#teacher"><i class="zmdi zmdi-plus"></i></a>';
	$("#programOption select")[0].selectedIndex=0;
	$("#programOption select")[1].selectedIndex=0;
	$("#serviceTime").val("");
	$("#duration").val("");
	$("#schoolYear span")[0].innerHTML="";
	$("#schoolYear select").show();
}
/*
	function:保存修改及添加服务量
	create:2017/12/15 15:28
	Author:Lingfeng Wei
*/
saveService=function(){
	if($("#serviceId").val()!=""){
		var serviceId=$("#serviceId").val();
		//var teacherId=$("#teacherOption select")[0].value;
		var project=$("#programOption select")[0].value;
		var service=$("#programOption select")[1].value;
		var serviceTime=$("#serviceTime").val();
		serviceTime=Date.parse(new Date(serviceTime))/1000;
		var duration=$("#duration").val();
		$.ajax({
			url:"/service/index/revise_single",
			type:"post",
			data:{"id":serviceId,"user_id":teacherId,"program":project,"project":service,"duration":duration,"date":serviceTime},
			success:function(data){		
				data=$.parseJSON(data);
			}
		})
	}
	else{
		var teacherId=$("#teacherOption select")[0].value;
		var project=$("#programOption select")[0].value;
		var service=$("#programOption select")[1].value;
		var serviceTime=$("#serviceTime").val();
		serviceTime=Date.parse(new Date(serviceTime))/1000;
		var duration=$("#duration").val();
		var school_team=$("#schoolYear select")[0].value;
		$.ajax({
			url:"/service/index/add",
			type:"post",
			data:{"user_id":teacherId,"program":project,"project":service,"school_term":school_team,"duration":duration,"date":serviceTime},
			success:function(data){		
				data=$.parseJSON(data);
			}
		})
	}
}
/*
	function:教师意见回复初始化
	create:2017/12/15 15:28
	@id			服务id
	@value		回复内容
	Author:Lingfeng Wei
*/
comment=function(id,val){
	$('#commentUser').val(id);
	$('#commentVal').val(val);
}
/*
	function:教师意见回复保存
	create:2017/12/15 15:28
	Author:Lingfeng Wei
*/
saveComment=function(){
	var id=$('#commentUser').val();
	var comment=$('#commentVal').val();
	$.ajax({
		type:"post",
		url:"/service/index/comment_submit",
		data:{"id":id,"comment":comment},
		success:function(data){
			data=$.parseJSON(data);
			if(data.status=200){
				
			}
			else{
				
			}
		}
	})
}
