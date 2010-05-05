//################## LOGGER ###########################################
var $RTC_log = {
  _DEBUG : 100,
  _INFO : 75,
  _WARN : 50,
  _ERROR : 0,
  LOG_LEVEL : 100,
  debug_on  :  typeof(console) == 'object' && this.LOG_LEVEL==this._DEBUG,

  /// utility logging methods that use firebug console if found
  debug : function(log_message, obj) {
    if(typeof(console) == 'object' && this.LOG_LEVEL>=this._DEBUG) {
      typeof(obj)!=='undefined'?console.debug(log_message, obj):console.debug(log_message);
    }
  },
  info : function(log_message, obj) {
    if(typeof(console) == 'object' && this.LOG_LEVEL>=this._INFO) {
      typeof(obj)!=='undefined'?console.info(log_message, obj):console.info(log_message);
    }
  },
  warn : function(log_message, obj) {
    if(typeof(console) == 'object' && this.LOG_LEVEL>=this._WARN) {
      typeof(obj)!=='undefined'?console.warn(log_message, obj):console.warn(log_message);
    }
  },
  error : function(log_message, obj) {
    if(typeof(console) == 'object' && this.LOG_LEVEL>=this._ERROR) {
      typeof(obj)!=='undefined'?console.error(log_message, obj):console.error(log_message);
    }
  }
}