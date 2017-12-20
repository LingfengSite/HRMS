Vue.filter('time', function (value) {
		let time=new Date(parseInt(value) * 1000);
			return formatDate(time,'yyyy-MM-dd hh:mm');
})
function formatDate(date,fmt){
    if(/(y+)/.test(fmt)){
        fmt = fmt.replace(RegExp.$1, (date.getFullYear()+'').substr(4-RegExp.$1.length));
    }
    let o = {
        'M+': date.getMonth()+1,
        'd+': date.getDate(),
        'h+': date.getHours(),
        'm+': date.getMinutes(),
        's+': date.getSeconds()
    }
    for(let k in o){    
        let str = o[k]+'';
        if(new RegExp(`(${k})`).test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length===1)?str:padLeftZero(str));
        }
    }
    return fmt;
};
function padLeftZero(str){
    return ('00'+str).substr(str.length);
}
String.prototype.isLike = function(exp, i){
    var str = this;
    i = i == null ? false : i;
    if (exp.constructor == String) {
        /* 首先将表达式中的‘_’替换成‘.’，但是‘[_]’表示对‘_’的转义，所以做特殊处理 */
        var s = exp.replace(/_/g, function(m, i) {
            if (i == 0 || i == exp.length - 1) {
                return ".";
            } else {
                if (exp.charAt(i - 1) == "[" && exp.charAt(i + 1) == "]") {
                    return m;
                }
                return ".";
            }
        });
         /* 将表达式中的‘%’替换成‘.’，但是‘[%]’表示对‘%’的转义，所以做特殊处理 */
        s = s.replace(/%/g, function(m, i) {
            if (i == 0 || i == s.length - 1) {
                return ".*";
            } else {
                if (s.charAt(i - 1) == "[" && s.charAt(i + 1) == "]") {
                    return m;
                }
                return ".*";
            }
        });

        /*将表达式中的‘[_]’、‘[%]’分别替换为‘_’、‘%’*/
        s = s.replace(/\[_\]/g, "_").replace(/\[%\]/g, "%");

        /*对表达式处理完后构造一个新的正则表达式，用以判断当前字符串是否和给定的表达式相似*/
        var regex = new RegExp("" + s, i ? "" : "i");
        return regex.test(this);
    }
    return false;
};
 