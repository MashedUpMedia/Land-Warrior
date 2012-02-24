
/*
/**
* CHRONOFORMS version 3.0 
* Copyright (c) 2008 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
You are not allowed to copy or use or rebrand or sell any code at this page under your own name or any other identity!
* See readme.html.
* Visit http://www.ChronoEngine.com for regular update and information.
**/
Tips.implement({
initialize: function(elements, lasthope,options){
		this.setOptions(options);
		this.lasthope = lasthope;
		this.toolTip = new Element('div', {
			'class': 'cf_'+this.options.className + '-tip',
			'id': this.options.className + '-tip-' + this.options.elementid,
			'styles': {
				'position': 'absolute',
				'top': '0',
				'left': '0',
				'visibility': 'hidden'
			}
		}).inject(document.body);
		this.wrapper = new Element('div').inject(this.toolTip);
		$$(elements).each(this.build, this);
		if (this.options.initialize) this.options.initialize.call(this);
	},

	build: function(el){
		el.$tmp.myTitle = (el.href && el.getTag() == 'a') ? el.href.replace('http://', '') : (el.rel || false);
		if (el.title){
			var dual = el.title.split('::');
			if (dual.length > 1){
				el.$tmp.myTitle = dual[0].trim();
				el.$tmp.myText = dual[1].trim();
			} else {
				el.$tmp.myText = el.title;
			}
			el.removeAttribute('title');
		} else {
			var dual = this.lasthope.split('::');
			if (dual.length > 1){
				el.$tmp.myTitle = dual[0].trim();
				el.$tmp.myText = dual[1].trim();
			} else {
				el.$tmp.myText = el.title;
			}
		}
		if (el.$tmp.myTitle && el.$tmp.myTitle.length > this.options.maxTitleChars) el.$tmp.myTitle = el.$tmp.myTitle.substr(0, this.options.maxTitleChars - 1) + "&hellip;";
		el.addEvent('mouseenter', function(event){
			this.start(el);
			if (!this.options.fixed) this.locate(event);
			else this.position(el);
		}.bind(this));
		if (!this.options.fixed) el.addEvent('mousemove', this.locate.bindWithEvent(this));
		var end = this.end.bind(this);
		el.addEvent('mouseleave', end);
		el.addEvent('trash', end);
	},
	start: function(el){
		this.wrapper.empty();
		if (el.$tmp.myTitle){
			this.title = new Element('span').inject(new Element('div', {'class': 'cf_'+this.options.className + '-title'}).inject(this.wrapper)).setHTML(el.$tmp.myTitle);
		}
		if (el.$tmp.myText){
			this.text = new Element('span').inject(new Element('div', {'class': 'cf_'+this.options.className + '-text'}).inject(this.wrapper)).setHTML(el.$tmp.myText);
		}
		$clear(this.timer);
		this.timer = this.show.delay(this.options.showDelay, this);
	}
});
Element.extend({
	showProperties: function(ParentTag){
		var Prop = new ELEMPROP(ParentTag, this);
		return Prop;
	}
});
/////////////////////////////////////////////// LABEL
var CFLABEL = new Class({
    initialize: function(style, text, forx){
        this.style = (style) ? style : 'cf_label';
		this.text = (text) ? text : 'Label';
		this.forx = (forx) ? forx : '';
    },
	createElement: function(){
		this.thelabel = new Element('label').setProperty("class", this.style);
		this.thelabel.setText(this.text);
		return this.thelabel;
	}
});

/////////////////////////////////////////////// SPAN
var CFSPAN = new Class({
    initialize: function(style, text, forx){
        this.style = (style) ? style : 'cf_text';
		this.text = (text) ? text : 'Label';
    },
	createElement: function(){
		this.thespan = new Element('span').setProperty("class", this.style);
		//this.thespan.setProperty('id', this.forx);
		this.thespan.setText(this.text);
		return this.thespan;
	}
});

/////////////////////////////////////////////// BUTTON
var CFBUTTON = new Class({
    initialize: function(style, text, forx){
        this.style = (style) ? style : 'cf_button';
		this.text = (text) ? text : 'Submit';
		this.name = (forx) ? forx : '';
    },
	createElement: function(){
		this.thebutton = new Element('input', {'name':this.forx, 'value':this.text, "type":'submit'});
		//this.thebutton.setProperty('name', this.forx);
		//this.thebutton.setProperty('value', this.text);
		return this.thebutton;
	}
});

/////////////////////////////////////////////// HEADING
var CFHEADING = new Class({
    initialize: function(style, text, forx){
        this.style = (style) ? style : 'cf_text';
		this.text = (text) ? text : 'Label';
		//this.id = (forx) ? forx : '';
    },
	createElement: function(){
		this.thehead = new Element('H1').setProperty("class", this.style);
		//this.thehead.setProperty('id', this.forx);
		this.thehead.setText(this.text);
		return this.thehead;
	}
});

/////////////////////////////////////////////// TEXT BOX
var CFTEXTBOX = new Class({
    initialize: function(style, size, name){
        this.style = (style) ? style : 'cf_inputbox';
		this.size = (size) ? size : '50';
		this.maxlength = '150';
		this.name = (name) ? name : '';
    },
	createElement: function(){
		this.thetextbox = new Element('input', {'name':this.name, 'type':'text', 'id':this.name, 'size':this.size, 'maxlength':this.maxlength}).setProperty("class", this.style);
		//this.thetextbox.setProperty('type', 'text');
		//this.thetextbox.setProperty('name', this.name);
		//this.thetextbox.setProperty('id', this.name);
		//this.thetextbox.setProperty('size', this.size);
		//this.thetextbox.setProperty('maxlength', this.maxlength);
		return this.thetextbox;
	}
});

/////////////////////////////////////////////// PASSWORD
var CFPASSWORD = new Class({
    initialize: function(style, size, name){
        this.style = (style) ? style : 'cf_inputbox';
		this.size = (size) ? size : '50';
		this.maxlength = '150';
		this.name = (name) ? name : '';
    },
	createElement: function(){
		this.thepassword = new Element('input', {'name':this.name, 'type':'password', 'id':this.name, 'size':this.size, 'maxlength':this.maxlength}).setProperty("class", this.style);
		//this.thepassword.setProperty('type', 'password');
		//this.thepassword.setProperty('name', this.name);
		//this.thepassword.setProperty('id', this.name);
		//this.thepassword.setProperty('size', this.size);
		//this.thepassword.setProperty('maxlength', this.maxlength);
		return this.thepassword;
	}
});

/////////////////////////////////////////////// HIDDEN
var CFHIDDEN = new Class({
    initialize: function(value, name){
		this.value = (value) ? value : '';
		this.name = (name) ? name : '';
    },
	createElement: function(){
		this.thehidden = new Element('input', {'name':this.name, 'type':'hidden', 'id':this.name, 'value':this.value});
		return this.thehidden;
	}
});
/////////////////////////////////////////////// DATE PICKER
var CFDATEPICKER = new Class({
    initialize: function(style, size, name){
        this.style = (style) ? style : 'cf_inputbox';
		this.size = (size) ? size : '50';
		this.name = (name) ? name : '';
    },
	createElement: function(){
		this.thedatepicker = new Element('input', {'name':this.name, 'type':'text', 'id':this.name, 'size':this.size}).setProperty("class", this.style);
		//this.thedatepicker.setProperty('type', 'text');
		//this.thedatepicker.setProperty('name', this.name);
		//this.thedatepicker.setProperty('id', this.name);
		//this.thedatepicker.setProperty('size', this.size);
		this.thedatepicker.addEvent('click', function(e) {
			new Calendar(this);
		});
		return this.thedatepicker;
	}
});

/////////////////////////////////////////////// FILE
var CFFILE = new Class({
    initialize: function(style, size, name){
        this.style = (style) ? style : 'cf_inputbox';
		this.size = (size) ? size : '20';
		this.name = (name) ? name : '';
    },
	createElement: function(){
		this.thefile = new Element('input', {'name':this.name, 'type':'file', 'id':this.name, 'size':this.size}).setProperty("class", this.style);
		//this.thefile.setProperty('type', 'file');
		//this.thefile.setProperty('name', this.name);
		//this.thefile.setProperty('id', this.name);
		//this.thefile.setProperty('size', this.size);
		return this.thefile;
	}
});

/////////////////////////////////////////////// CAPTCHA
var CFCAPTCHA = new Class({
    initialize: function(style, size, name){
        this.text = '{imageverification}';
    },
	createElement: function(){
		this.thecaptcha = new Element('span');
		this.thecaptcha.setText(this.text);
		return this.thecaptcha;
	}
});

/////////////////////////////////////////////// TEXT AREA
var CFTEXTAREA = new Class({
    initialize: function(style, cols, rows, name){
        this.style = (style) ? style : 'cf_inputbox';
		this.cols = (cols) ? cols : '30';
		this.rows = (rows) ? rows : '3';
		this.name = (name) ? name : '';
    },
	createElement: function(){
		this.thetextarea = new Element('textarea', {'name':this.name, 'cols':this.cols, 'id':this.name, 'rows':this.rows}).setProperty("class", this.style);
		//this.thetextarea.setProperty('name', this.name);
		//this.thetextarea.setProperty('id', this.name);
		//this.thetextarea.setProperty('cols', this.cols);
		//this.thetextarea.setProperty('rows', this.rows);
		return this.thetextarea;
	}
});

/////////////////////////////////////////////// DROPDOWN
var CFSELECT = new Class({
    initialize: function(style, size, name){
        this.style = (style) ? style : 'cf_inputbox';
		this.size = (size) ? size : '1';
		this.name = (name) ? name : '';
    },
	createElement: function(){
		this.theselect = new Element('select', {'name':this.name, 'size':this.size, 'id':this.name}).setProperty("class", this.style);
		//this.theselect.setProperty('name', this.name);
		//this.theselect.setProperty('id', this.name);
		//this.theselect.setProperty('size', this.size);
		this.newoption = new Element('option');
		this.newoption.setText('option 1');
		this.newoption.setProperty('value', 'option 1');
		this.newoption.injectInside(this.theselect);
		this.newoption2 = new Element('option');
		this.newoption2.setText('option 2');
		this.newoption2.setProperty('value', 'option 2');
		this.newoption2.injectInside(this.theselect);
		this.newoption3 = new Element('option');
		this.newoption3.setText('option 3');
		this.newoption3.setProperty('value', 'option 3');
		this.newoption3.injectInside(this.theselect);
		return this.theselect;
	}
});

/////////////////////////////////////////////// CHECKBOX
var CFCHECKBOX = new Class({
    initialize: function(style, size, name){
        this.style = (style) ? style : 'cf_inputbox';
		this.size = (size) ? size : '1';
		this.name = (name) ? name : '';
    },
	createElement: function(){
		this.thegroup = new Element('div').setProperty("class", 'float_left');
		var count = $('left_column').getElements('input[type=checkbox]').length;
		for (var i = 1; i < 4; i++) {
			this.newcheck = new Element('input', {'name':this.name+'_'+(i+count), 'value':'check '+(i+count), 'id':this.name+'_'+(i+count), 'class':'radio', "type":'checkbox'});
			//this.newcheck.setProperty('value', 'check '+(i+count));
			//this.newcheck.setProperty('name', this.name+'_'+(i+count));
			//this.newcheck.setProperty('id', this.name+'_'+(i+count));
			//this.newcheck.setProperty('class', 'radio');
			this.newcheck.injectInside(this.thegroup);
			this.thelabel = new Element('label').setProperty("class", 'check_label');
			this.thelabel.setProperty('for', this.name+'_'+(i+count));
			this.thelabel.setText('Check '+(i+count));
			this.thelabel.injectInside(this.thegroup);
			linebreak = new Element('br');
			linebreak.injectInside(this.thegroup);
		}
		///////// 2
		/*this.newcheck2 = new Element('input').setProperty("type", 'checkbox');
		this.newcheck2.setProperty('value', 'check 2');
		this.newcheck2.setProperty('name', this.name+'_2');
		this.newcheck2.setProperty('id', this.name+'_2');
		this.newcheck2.setProperty('class', 'radio');
		this.newcheck2.injectInside(this.thegroup);
		this.thelabel2 = new Element('label').setProperty("class", 'check_label');
		this.thelabel2.setProperty('for', this.name+'_2');
		this.thelabel2.setText('Check 2');
		this.thelabel2.injectInside(this.thegroup);
		linebreak = new Element('br');
		linebreak.injectInside(this.thegroup);
		////// 3
		this.newcheck3 = new Element('input').setProperty("type", 'checkbox');
		this.newcheck3.setProperty('value', 'check 3');
		this.newcheck3.setProperty('name', this.name+'_3');
		this.newcheck3.setProperty('id', this.name+'_3');
		this.newcheck3.setProperty('class', 'radio');
		this.newcheck3.injectInside(this.thegroup);
		this.thelabel3 = new Element('label').setProperty("class", 'check_label');
		this.thelabel3.setProperty('for', this.name+'_3');
		this.thelabel3.setText('Check 3');
		this.thelabel3.injectInside(this.thegroup);
		linebreak = new Element('br');
		linebreak.injectInside(this.thegroup);*/
		return this.thegroup;
	}
});

/////////////////////////////////////////////// RADIO
var CFRADIO = new Class({
    initialize: function(style, size, name){
        this.style = (style) ? style : 'cf_inputbox';
		this.size = (size) ? size : '1';
		this.name = (name) ? name : '';
    },
	createElement: function(){
		//alert($('left_column').getElements('input[type=radio]').length);
		var count = $('left_column').getElements('input[type=radio]').length;
		this.thegroup = new Element('div').setProperty("class", 'float_left');
		for (var i = 1; i < 4; i++) {
			this.newradio = new Element('input', {'name':this.name+count, 'value':'radio '+(i+count), 'id':this.name+'_'+(i+count), 'class':'radio', "type":'radio'});
			//this.newradio.setProperty('value', 'radio '+(i+count));
			//this.newradio.setProperty('name', this.name+count);
			//this.newradio.setProperty('id', this.name+'_'+(i+count));
			//this.newradio.setProperty('class', 'radio');
			this.newradio.injectInside(this.thegroup);
			this.thelabel = new Element('label').setProperty("class", 'radio_label');
			this.thelabel.setProperty('for', this.name+'_'+(i+count));
			this.thelabel.setText('Radio '+(i+count));
			this.thelabel.injectInside(this.thegroup);
			linebreak = new Element('br');
			linebreak.injectInside(this.thegroup);
		}
		///////// 2
		/*this.newradio2 = new Element('input').setProperty("type", 'radio');
		this.newradio2.setProperty('value', 'radio 2');
		this.newradio2.setProperty('name', this.name);
		this.newradio2.setProperty('id', this.name+'_2');
		this.newradio2.setProperty('class', 'radio');
		this.newradio2.injectInside(this.thegroup);
		this.thelabel2 = new Element('label').setProperty("class", 'radio_label');
		this.thelabel2.setProperty('for', this.name+'_2');
		this.thelabel2.setText('Radio 2');
		this.thelabel2.injectInside(this.thegroup);
		linebreak = new Element('br');
		linebreak.injectInside(this.thegroup);
		////// 3
		this.newradio3 = new Element('input').setProperty("type", 'radio');
		this.newradio3.setProperty('value', 'radio 3');
		this.newradio3.setProperty('name', this.name);
		this.newradio3.setProperty('id', this.name+'_3');
		this.newradio3.setProperty('class', 'radio');
		this.newradio3.injectInside(this.thegroup);
		this.thelabel3 = new Element('label').setProperty("class", 'radio_label');
		this.thelabel3.setProperty('for', this.name+'_3');
		this.thelabel3.setText('Radio 3');
		this.thelabel3.injectInside(this.thegroup);
		linebreak = new Element('br');
		linebreak.injectInside(this.thegroup);*/
		return this.thegroup;
	}
});


////////////// Properties
var ELEMPROP = new Class({
	initialize: function(ParentTag, element){
		$$('div.Propertiesitem').each(function(item){
			item.setStyle('display','none');
		});
		
			////////////////////////////// LABEL
		if(ParentTag == 'label'){
			function saveLabel(){
				element.setText($('prop_'+ParentTag+'_text').value);
			}
			$('prop_'+ParentTag).setStyle('display','block');
			$('prop_'+ParentTag+'_text').value = element.getText();
			$('prop_'+ParentTag+'_done').removeEvents();
			$('prop_'+ParentTag+'_done').addEvent('click', saveLabel.bindWithEvent(element));
			
			////////////////////////////// TEXT
		//} else if(ParentTag == 'span'){
		} else if (element.getFirst().hasClass('cf_text')){
			actual_element = element.getFirst().getFirst();
			function saveSpan(){
				actual_element.setText($('prop_cf_text_text').value);
			}
			$('prop_cf_text').setStyle('display','block');
			$('prop_cf_text_text').value = actual_element.getText();
			$('prop_cf_text_done').removeEvents();
			$('prop_cf_text_done').addEvent('click', saveSpan.bindWithEvent(actual_element));
			
			////////////////////////////// HEADINGs
		//} else if((ParentTag == 'h1')||(ParentTag == 'h2')||(ParentTag == 'h3')||(ParentTag == 'h4')||(ParentTag == 'h5')||(ParentTag == 'h6')){
		} else if (element.getFirst().hasClass('cf_heading')){
			actual_element = element.getFirst().getFirst();
			function saveHead(){
				actual_element.setText($('prop_cf_heading_text').value);
				newhead = new Element($('prop_cf_heading_size').value).setProperty("class", actual_element.getProperty('class'));
				newhead.setProperty('id', actual_element.getProperty('id'));
				newhead.setText(actual_element.getText());
				
				/*newhead.addEvent('click', function(e) {
					new Event(e).stop();
					$$('.form_item').each(function(item2){
						item2.setStyle('border', '0px solid #000');
						$E('.delete_icon', item2).setStyle('display', 'none');
					});
					this.getParent().getParent().effect('background-color', {wait: false, duration: 100}).start('ffffff','ffffff');
					this.getParent().getParent().setStyle('border', '1px solid #000');
					//this.getParent().getParent().getLast().setStyle('display', 'inline');
					$E('.delete_icon', this.getParent().getParent()).setStyle('display', 'inline');
					this.showProperties(this.getTag());
				})*/
				element.showProperties(newhead.getTag());				
				actual_element.replaceWith(newhead);				
			}
			$('prop_cf_heading').setStyle('display','block');
			$('prop_cf_heading_text').value = actual_element.getText();
			$('prop_cf_heading_size').value = actual_element.getTag().capitalize();
			$('prop_cf_heading_done').removeEvents();
			$('prop_cf_heading_done').addEvent('click', saveHead.bindWithEvent(actual_element));
			
			////////////////////////////// TEXT BOX
		//} else if ((ParentTag == 'input')&&(element.getProperty('type') == 'text')&&(!element.hasClass('cf_datetime'))){
		} else if (element.getFirst().hasClass('cf_textbox')){
			actual_element = $E('input[type=text]', element);
			function saveTextBox(){
				$E('label', element).setText($('prop_cf_textbox_label').value);
				actual_element.setProperty('size', $('prop_cf_textbox_size').value);
				actual_element.setProperty('maxlength', $('prop_cf_textbox_max').value);
				$('prop_cf_textbox').getElements('input[name^=validation_]').each(function(checkbox){
					actual_element.removeClass(checkbox.value);
				});
				$('prop_cf_textbox').getElements('input[name^=validation_]').each(function(checkbox){
					if(checkbox.checked)
					actual_element.addClass(checkbox.value);
				});
				
				
				
				if($('prop_cf_textbox_description').value.trim() != ''){
					if($chk($E('div.tooltipdiv', element))){
						tooltipdiv = $E('div.tooltipdiv', element);
							
						tooltipdiv.setText($('prop_cf_textbox_label').value+' :: '+$('prop_cf_textbox_description').value.trim());
						tooltiplink = $E('a.tooltiplink', element);
						if($chk($('tool-tip-'+actual_element.getProperty('id')))){
							$('tool-tip-'+actual_element.getProperty('id')).remove();
						}
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}else{
						tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
						tooltipdiv.setText($('prop_cf_textbox_label').value+' :: '+$('prop_cf_textbox_description').value.trim());
						tooltipdiv.injectAfter(actual_element);
						tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 10px'}, 'width':"16", 'height':"16"});
						tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
						tooltipimg.injectInside(tooltiplink);
						tooltiplink.injectAfter(actual_element);
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}
				}else{
					if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
					if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
					if($chk($('tool-tip-'+actual_element.getProperty('id')))){
						$('tool-tip-'+actual_element.getProperty('id')).remove();
					}
				}
				
				
			}
			if($chk($E('div.tooltipdiv', element))){
				$('prop_cf_textbox_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
			}else{
				$('prop_cf_textbox_description').value = '';
			}
			$('prop_cf_textbox').getElements('input[name^=validation_]').each(function(checkbox){
				if(actual_element.hasClass(checkbox.value))checkbox.checked = true;
				else checkbox.checked = false;
			});
			$('prop_cf_textbox').setStyle('display','block');
			$('prop_cf_textbox_label').value = $E('label', element).getText();
			$('prop_cf_textbox_size').value = actual_element.getProperty('size');
			$('prop_cf_textbox_max').value = actual_element.getProperty('maxlength');
			$('prop_cf_textbox_done').removeEvents();
			$('prop_cf_textbox_done').addEvent('click', saveTextBox.bindWithEvent(actual_element));
			
			////////////////////////////// DATE PICKER
		//} else if ((ParentTag == 'input')&&(element.getProperty('type') == 'text')&&(element.hasClass('cf_datetime'))){
		} else if (element.getFirst().hasClass('cf_datetimepicker')){
			actual_element = $E('input[type=text]', element);
			function saveDatePicker(){
				$E('label', element).setText($('prop_cf_datetimepicker_label').value);
				actual_element.setProperty('size', $('prop_cf_datetimepicker_size').value);
				//element.setProperty('maxlength', $('prop_datepicker_max').value);
				$('prop_cf_datetimepicker').getElements('input[name^=validation_]').each(function(checkbox){
					actual_element.removeClass(checkbox.value);
				});
				$('prop_cf_datetimepicker').getElements('input[name^=validation_]').each(function(checkbox){
					if(checkbox.checked)
					actual_element.addClass(checkbox.value);
				});
				if($('prop_cf_datetimepicker_description').value.trim() != ''){
					if($chk($E('div.tooltipdiv', element))){
						tooltipdiv = $E('div.tooltipdiv', element);
							
						tooltipdiv.setText($('prop_cf_datetimepicker_label').value+' :: '+$('prop_cf_datetimepicker_description').value.trim());
						tooltiplink = $E('a.tooltiplink', element);
						if($chk($('tool-tip-'+actual_element.getProperty('id')))){
							$('tool-tip-'+actual_element.getProperty('id')).remove();
						}
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}else{
						tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
						tooltipdiv.setText($('prop_cf_datetimepicker_label').value+' :: '+$('prop_cf_datetimepicker_description').value.trim());
						tooltipdiv.injectAfter(actual_element);
						tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 10px'}, 'width':"16", 'height':"16"});
						tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
						tooltipimg.injectInside(tooltiplink);
						tooltiplink.injectAfter(actual_element);
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}
				}else{
					if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
					if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
					if($chk($('tool-tip-'+actual_element.getProperty('id')))){
						$('tool-tip-'+actual_element.getProperty('id')).remove();
					}
				}
				
				
			}
			if($chk($E('div.tooltipdiv', element))){
				$('prop_cf_datetimepicker_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
			}else{
				$('prop_cf_datetimepicker_description').value = '';
			}
			$('prop_cf_datetimepicker').getElements('input[name^=validation_]').each(function(checkbox){
				if(actual_element.hasClass(checkbox.value))checkbox.checked = true;
				else checkbox.checked = false;
			});
			$('prop_cf_datetimepicker').setStyle('display','block');
			$('prop_cf_datetimepicker_size').value = actual_element.getProperty('size');
			$('prop_cf_datetimepicker_label').value = $E('label', element).getText();
			//$('prop_datepicker_max').value = actual_element.getProperty('maxlength');
			$('prop_cf_datetimepicker_done').removeEvents();
			$('prop_cf_datetimepicker_done').addEvent('click', saveDatePicker.bindWithEvent(actual_element));
			
			////////////////////////////// TEXTAREA
		//} else if (ParentTag == 'textarea'){
		} else if (element.getFirst().hasClass('cf_textarea')){
			actual_element = $E('textarea', element);
			function saveTextArea(){
				$E('label', element).setText($('prop_cf_textarea_label').value);
				actual_element.setProperty('rows', $('prop_cf_textarea_rows').value);
				actual_element.setProperty('cols', $('prop_cf_textarea_cols').value);
				$('prop_cf_textarea').getElements('input[name^=validation_]').each(function(checkbox){
					actual_element.removeClass(checkbox.value);
				});
				$('prop_cf_textarea').getElements('input[name^=validation_]').each(function(checkbox){
					if(checkbox.checked)
					actual_element.addClass(checkbox.value);
				});
				if($('prop_cf_textarea_description').value.trim() != ''){
					if($chk($E('div.tooltipdiv', element))){
						tooltipdiv = $E('div.tooltipdiv', element);
							
						tooltipdiv.setText($('prop_cf_textarea_label').value+' :: '+$('prop_cf_textarea_description').value.trim());
						tooltiplink = $E('a.tooltiplink', element);
						if($chk($('tool-tip-'+actual_element.getProperty('id')))){
							$('tool-tip-'+actual_element.getProperty('id')).remove();
						}
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}else{
						tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
						tooltipdiv.setText($('prop_cf_textarea_label').value+' :: '+$('prop_cf_textarea_description').value.trim());
						tooltipdiv.injectAfter(actual_element);
						tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 10px'}, 'width':"16", 'height':"16"});
						tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
						tooltipimg.injectInside(tooltiplink);
						tooltiplink.injectAfter(actual_element);
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}
				}else{
					if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
					if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
					if($chk($('tool-tip-'+actual_element.getProperty('id')))){
						$('tool-tip-'+actual_element.getProperty('id')).remove();
					}
				}
				
				
			}
			if($chk($E('div.tooltipdiv', element))){
				$('prop_cf_textarea_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
			}else{
				$('prop_cf_textarea_description').value = '';
			}
			$('prop_cf_textarea').getElements('input[name^=validation_]').each(function(checkbox){
				if(actual_element.hasClass(checkbox.value))checkbox.checked = true;
				else checkbox.checked = false;
			});
			$('prop_cf_textarea').setStyle('display','block');
			$('prop_cf_textarea_label').value = $E('label', element).getText();
			$('prop_cf_textarea_rows').value = actual_element.getProperty('rows');
			$('prop_cf_textarea_cols').value = actual_element.getProperty('cols');
			$('prop_cf_textarea_done').removeEvents();
			$('prop_cf_textarea_done').addEvent('click', saveTextArea.bindWithEvent(actual_element));
			
			////////////////////////////// DROPDOWN
		//} else if (ParentTag == 'select'){
		} else if (element.getFirst().hasClass('cf_dropdown')){
			actual_element = $E('select', element);
			function saveSelect(){
				$E('label', element).setText($('prop_cf_dropdown_label').value);
				$('prop_cf_dropdown').getElements('input[name^=validation_]').each(function(checkbox){
					actual_element.removeClass(checkbox.value);
				});
				$('prop_cf_dropdown').getElements('input[name^=validation_]').each(function(checkbox){
					if(checkbox.checked)
					actual_element.addClass(checkbox.value);
				});
				/*actual_element.getElements('option').each(function(option){
					option.remove();
				});*/
				newselect = new Element('select', {'name':actual_element.getProperty('name'), 'size':$('prop_cf_dropdown_size').value, 'id':actual_element.getProperty('id')}).setProperty("class", actual_element.getProperty('class'));				
				//actual_element.setProperty('size', $('prop_cf_dropdown_size').value);
				if($('prop_cf_dropdown_size').value.toInt() > 1){
					newselect.setProperty('multiple', '1');
					newselect.setProperty('name', actual_element.getProperty('name').replace('[]','')+'[]');
				}else{
					newselect.setProperty('name', actual_element.getProperty('name').replace('[]',''));
				}
				$('prop_cf_dropdown_options').value.split(',').each(function(option){
					if(option.trim()){															
						newoption = new Element('option');
						newoption.setText(option);
						newoption.setProperty('value', option);
						newoption.injectInside(newselect);
					}
				});
				actual_element.replaceWith(newselect);
				if($('prop_cf_dropdown_description').value.trim() != ''){
					if($chk($E('div.tooltipdiv', element))){
						tooltipdiv = $E('div.tooltipdiv', element);
							
						tooltipdiv.setText($('prop_cf_dropdown_label').value+' :: '+$('prop_cf_dropdown_description').value.trim());
						tooltiplink = $E('a.tooltiplink', element);
						if($chk($('tool-tip-'+newselect.getProperty('id')))){
							$('tool-tip-'+newselect.getProperty('id')).remove();
						}
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:newselect.getProperty('id')});
					}else{
						tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
						tooltipdiv.setText($('prop_cf_dropdown_label').value+' :: '+$('prop_cf_dropdown_description').value.trim());
						tooltipdiv.injectAfter(newselect);
						tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 10px'}, 'width':"16", 'height':"16"});
						tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
						tooltipimg.injectInside(tooltiplink);
						tooltiplink.injectAfter(newselect);
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:newselect.getProperty('id')});
					}
				}else{
					if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
					if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
					if($chk($('tool-tip-'+newselect.getProperty('id')))){
						$('tool-tip-'+newselect.getProperty('id')).remove();
					}
				}
				newselect.getParent().getParent().showProperties(newselect.getParent().getParent().getTag());
				
			}
			if($chk($E('div.tooltipdiv', element))){
				$('prop_cf_dropdown_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
			}else{
				$('prop_cf_dropdown_description').value = '';
			}
			$('prop_cf_dropdown').getElements('input[name^=validation_]').each(function(checkbox){
				if(actual_element.hasClass(checkbox.value))checkbox.checked = true;
				else checkbox.checked = false;
			});
			$('prop_cf_dropdown').setStyle('display','block');
			$('prop_cf_dropdown_label').value = $E('label', element).getText();
			$('prop_cf_dropdown_size').value = actual_element.getProperty('size');
			$('prop_cf_dropdown_options').setText('');
			actual_element.getElements('option').each(function(option){
				$('prop_cf_dropdown_options').setText($('prop_cf_dropdown_options').innerHTML + option.getProperty('value') +',');
			});
			$('prop_cf_dropdown_done').removeEvents();
			$('prop_cf_dropdown_done').addEvent('click', saveSelect.bindWithEvent(actual_element));
			
			////////////////////////////// CHECKBOXes BOX
		//} else if(ParentTag == 'div'){
			//if(element.getFirst().getProperty('type') == 'checkbox'){
		} else if (element.getFirst().hasClass('cf_checkbox')){
			actual_element = $E('div[class=float_left]', element);
				ParentTag = actual_element.getFirst().getProperty('type');
				function saveCheckbox(){
					$E('label', element).setText($('prop_cf_checkbox_label').value);
					actual_element.setHTML('');
					//check_number = 1;
					$('prop_cf_checkbox_options').value.split(',').each(function(check){
						if(check.trim()){															
							newcheck = new Element('input', {'name':check.replace(" ", "_"), 'value':check, 'id':check.replace(" ", "_"), 'class':'radio', "type":'checkbox'});
							//newcheck.setProperty('value', check);
							//newcheck.setProperty('name', check.replace(" ", "_"));
							//newcheck.setProperty('id', check.replace(" ", "_"));
							//newcheck.setProperty('class', 'radio');
							newcheck.injectInside(actual_element);
							checklabel = new Element('label').setProperty("class", 'check_label');
							checklabel.setProperty('for', check.replace(" ", "_"));
							checklabel.setText(check.capitalize());
							checklabel.injectInside(actual_element);
							linebreak = new Element('br');
							linebreak.injectInside(actual_element);
						}
					});
					$('prop_cf_checkbox').getElements('input[name^=validation_]').each(function(checkbox){
						$E('input[type=checkbox]', actual_element).removeClass(checkbox.value);
					});
					$('prop_cf_checkbox').getElements('input[name^=validation_]').each(function(checkbox){
						if(checkbox.checked)
						$E('input[type=checkbox]', actual_element).addClass(checkbox.value);
					});
					if($('prop_cf_checkbox_description').value.trim() != ''){
						if($chk($E('div.tooltipdiv', element))){
							tooltipdiv = $E('div.tooltipdiv', element);
								
							tooltipdiv.setText($('prop_cf_checkbox_label').value+' :: '+$('prop_cf_checkbox_description').value.trim());
							tooltiplink = $E('a.tooltiplink', element);
							if($chk($('tool-tip-'+actual_element.getProperty('id')))){
								$('tool-tip-'+actual_element.getProperty('id')).remove();
							}
							var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
						}else{
							tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
							tooltipdiv.setText($('prop_cf_checkbox_label').value+' :: '+$('prop_cf_checkbox_description').value.trim());
							tooltipdiv.injectAfter(actual_element);
							tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 10px'}, 'width':"16", 'height':"16"});
							tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
							tooltipimg.injectInside(tooltiplink);
							tooltiplink.injectAfter(actual_element);
							var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
						}
					}else{
						if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
						if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
						if($chk($('tool-tip-'+actual_element.getProperty('id')))){
							$('tool-tip-'+actual_element.getProperty('id')).remove();
						}
					}
					
					
				}
				if($chk($E('div.tooltipdiv', element))){
					$('prop_cf_checkbox_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
				}else{
					$('prop_cf_checkbox_description').value = '';
				}
				$('prop_cf_checkbox').getElements('input[name^=validation_]').each(function(checkbox){
					if($E('input[type=checkbox]', actual_element).hasClass(checkbox.value))checkbox.checked = true;
					else checkbox.checked = false;
				});
				$('prop_cf_checkbox').setStyle('display','block');
				$('prop_cf_checkbox_label').value = $E('label', element).getText();
				$('prop_cf_checkbox_options').setText('');
				actual_element.getElements('input').each(function(input){//alert($('prop_cf_checkbox_options').innerHTML); alert(input.getProperty('value'));
					$('prop_cf_checkbox_options').setText($('prop_cf_checkbox_options').innerHTML + input.getProperty('value') +',');
				});
				$('prop_cf_checkbox_done').removeEvents();
				$('prop_cf_checkbox_done').addEvent('click', saveCheckbox.bindWithEvent(actual_element));
				
				////////////////////////////// RADIOs BOX
			//} else if(element.getFirst().getProperty('type') == 'radio'){
		} else if (element.getFirst().hasClass('cf_radiobutton')){
			actual_element = $E('div[class=float_left]', element);
				ParentTag = actual_element.getFirst().getProperty('type');
				function saveRadio(){
					$E('label', element).setText($('prop_cf_radiobutton_label').value);
					actual_element.setHTML('');
					//check_number = 1;
					var count = $('left_column').getElements('input[type=radio]').length;
					$('prop_cf_radiobutton_options').value.split(',').each(function(radio){
						if(radio.trim()){															
							newradio = new Element('input', {'name':'radio'+count, 'value':radio, 'id':radio.replace(" ", "_")+count, 'class':'radio', "type":'radio'});
							//newradio.setProperty('value', radio);
							//newradio.setProperty('name', 'radio'+count);
							//newradio.setProperty('id', radio.replace(" ", "_")+count);
							//newradio.setProperty('class', 'radio');
							newradio.injectInside(actual_element);
							radiolabel = new Element('label').setProperty("class", 'radio_label');
							radiolabel.setProperty('for', radio.replace(" ", "_")+count);
							radiolabel.setText(radio.capitalize());
							radiolabel.injectInside(actual_element);
							linebreak = new Element('br');
							linebreak.injectInside(actual_element);
						}
					});
					$('prop_cf_radiobutton').getElements('input[name^=validation_]').each(function(checkbox){
						$E('input[type=radio]', actual_element).removeClass(checkbox.value);
					});
					$('prop_cf_radiobutton').getElements('input[name^=validation_]').each(function(checkbox){
						if(checkbox.checked)
						$E('input[type=radio]', actual_element).addClass(checkbox.value);
					});
					if($('prop_cf_radiobutton_description').value.trim() != ''){
						if($chk($E('div.tooltipdiv', element))){
							tooltipdiv = $E('div.tooltipdiv', element);
								
							tooltipdiv.setText($('prop_cf_radiobutton_label').value+' :: '+$('prop_cf_radiobutton_description').value.trim());
							tooltiplink = $E('a.tooltiplink', element);
							if($chk($('tool-tip-'+actual_element.getProperty('id')))){
								$('tool-tip-'+actual_element.getProperty('id')).remove();
							}
							var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
						}else{
							tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
							tooltipdiv.setText($('prop_cf_radiobutton_label').value+' :: '+$('prop_cf_radiobutton_description').value.trim());
							tooltipdiv.injectAfter(actual_element);
							tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 10px'}, 'width':"16", 'height':"16"});
							tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
							tooltipimg.injectInside(tooltiplink);
							tooltiplink.injectAfter(actual_element);
							var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
						}
					}else{
						if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
						if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
						if($chk($('tool-tip-'+actual_element.getProperty('id')))){
							$('tool-tip-'+actual_element.getProperty('id')).remove();
						}
					}
					
					
				}
				if($chk($E('div.tooltipdiv', element))){
					$('prop_cf_radiobutton_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
				}else{
					$('prop_cf_radiobutton_description').value = '';
				}
				$('prop_cf_radiobutton').getElements('input[name^=validation_]').each(function(checkbox){
					if($E('input[type=radio]', actual_element).hasClass(checkbox.value))checkbox.checked = true;
					else checkbox.checked = false;
				});
				$('prop_cf_radiobutton').setStyle('display','block');
				$('prop_cf_radiobutton_label').value = $E('label', element).getText();
				$('prop_cf_radiobutton_options').setText('');
				actual_element.getElements('input').each(function(input){
					$('prop_cf_radiobutton_options').setText($('prop_cf_radiobutton_options').innerHTML + input.getProperty('value') +',');
				});
				$('prop_cf_radiobutton_done').removeEvents();
				$('prop_cf_radiobutton_done').addEvent('click', saveRadio.bindWithEvent(actual_element));
			//}
			
			////////////////////////////// SUBMIT BUTTON
		//} else if ((ParentTag == 'input')&&(element.getProperty('type') == 'submit')){
		} else if (element.getFirst().hasClass('cf_button')){
			actual_element = $E('input[type=submit]', element);
			function saveButton(){
				actual_element.setProperty('value', $('prop_cf_button_text').value);
				if($('prop_cf_button_reset').value == '1'){
					Reset = $E('input[type=reset]', element);
					if(!Reset){
						Reset = new Element('input').setProperty("type", 'reset');
						Reset.setProperty('value', 'Reset');
						Reset.injectAfter(actual_element);
					}
				}else{
					Reset = $E('input[type=reset]', element);
					if(Reset)Reset.remove();
				}
				if($('prop_cf_button_description').value.trim() != ''){
					if($chk($E('div.tooltipdiv', element))){
						tooltipdiv = $E('div.tooltipdiv', element);
							
						tooltipdiv.setText($('prop_cf_button_text').value+' :: '+$('prop_cf_button_description').value.trim());
						tooltiplink = $E('a.tooltiplink', element);
						if($chk($('tool-tip-'+actual_element.getProperty('id')))){
							$('tool-tip-'+actual_element.getProperty('id')).remove();
						}
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}else{
						tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
						tooltipdiv.setText($('prop_cf_button_text').value+' :: '+$('prop_cf_button_description').value.trim());
						tooltipdiv.injectAfter(actual_element);
						tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 10px'}, 'width':"16", 'height':"16"});
						tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
						tooltipimg.injectInside(tooltiplink);
						tooltiplink.injectAfter(actual_element);
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}
				}else{
					if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
					if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
					if($chk($('tool-tip-'+actual_element.getProperty('id')))){
						$('tool-tip-'+actual_element.getProperty('id')).remove();
					}
				}
				
				
			}
			if($chk($E('div.tooltipdiv', element))){
				$('prop_cf_button_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
			}else{
				$('prop_cf_button_description').value = '';
			}
			$('prop_cf_button').setStyle('display','block');
			Resetb = $E('input[type=reset]', element);
			if(Resetb)$('prop_cf_button_reset').value = '1';
			$('prop_cf_button_text').value = actual_element.getProperty('value');
			$('prop_cf_button_done').removeEvents();
			$('prop_cf_button_done').addEvent('click', saveButton.bindWithEvent(actual_element));
			
			////////////////////////////// FILE UPLOAD BOX
		//} else if ((ParentTag == 'input')&&(element.getProperty('type') == 'file')){
		} else if (element.getFirst().hasClass('cf_fileupload')){
			actual_element = $E('input[type=file]', element);
			function saveFile(){
				$E('label', element).setText($('prop_cf_fileupload_label').value);
				
				//var uploadfields = new Array();
				//var field;
				if($('uploadfields').value.trim()){
					var uploadfields = $('uploadfields').value.trim().split(',');
					var i = 0;
					var set = 0;
					uploadfields.each(function(field){														
						//alert(field);
						var field_data = field.split(':');
						if(field_data[0] == actual_element.getProperty('id')){
							uploadfields[i] = actual_element.getProperty('id')+ ':' + $('prop_cf_fileupload_extensions').value + '{' + $('prop_cf_fileupload_maxsize').value + '-' + $('prop_cf_fileupload_minsize').value + '}';
							//alert(uploadfields[i]);
							set = 1;
						}else{
							//nothing
						}
						i = i + 1;
					});
					if(!set)uploadfields[uploadfields.length] = actual_element.getProperty('id')+ ':' + $('prop_cf_fileupload_extensions').value + '{' + $('prop_cf_fileupload_maxsize').value + '-' + $('prop_cf_fileupload_minsize').value + '}';
					$('uploadfields').value = uploadfields.join(',');
				}else{
					$('uploadfields').value = actual_element.getProperty('id')+ ':' + $('prop_cf_fileupload_extensions').value + '{' + $('prop_cf_fileupload_maxsize').value + '-' + $('prop_cf_fileupload_minsize').value + '}';
				}
				//alert(uploadfields.length);
				/*for(var field in uploadfields){alert(field);
					var field_data = uploadfields[field].split(':');
					if(field_data == actual_element.getProperty('id')){uploadfields[field] = actual_element.getProperty('id')+ ':' + $('prop_cf_fileupload_extensions').value;}
				}
				$('uploadfields').value = uploadfields.join(',');*/
				//$('uploadfields').value = actual_element.getProperty('id')+ ':' + $('prop_cf_fileupload_extensions').value;
				//actual_element.setProperty('value', $('prop_'+actual_element.getProperty('type')+'_text').value);
				if($('prop_cf_fileupload_description').value.trim() != ''){
					if($chk($E('div.tooltipdiv', element))){
						tooltipdiv = $E('div.tooltipdiv', element);
							
						tooltipdiv.setText($('prop_cf_fileupload_label').value+' :: '+$('prop_cf_fileupload_description').value.trim());
						tooltiplink = $E('a.tooltiplink', element);
						if($chk($('tool-tip-'+actual_element.getProperty('id')))){
							$('tool-tip-'+actual_element.getProperty('id')).remove();
						}
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}else{
						tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
						tooltipdiv.setText($('prop_cf_fileupload_label').value+' :: '+$('prop_cf_fileupload_description').value.trim());
						tooltipdiv.injectAfter(actual_element);
						tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 80px'}, 'width':"16", 'height':"16"});
						tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
						tooltipimg.injectInside(tooltiplink);
						tooltiplink.injectAfter(actual_element);
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}
				}else{
					if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
					if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
					if($chk($('tool-tip-'+actual_element.getProperty('id')))){
						$('tool-tip-'+actual_element.getProperty('id')).remove();
					}
				}
				
				
			}
			if($chk($E('div.tooltipdiv', element))){
				$('prop_cf_fileupload_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
			}else{
				$('prop_cf_fileupload_description').value = '';
			}
			$('prop_cf_fileupload').setStyle('display','block');
			$('prop_cf_fileupload_label').value = $E('label', element).getText();
			
			$('prop_cf_fileupload_extensions').value = '';
			$('prop_cf_fileupload_maxsize').value = '';
			$('prop_cf_fileupload_minsize').value = '';
			var uploadfields = $('uploadfields').value.trim().split(',');
			uploadfields.each(function(field){
				var field_data = field.split(':');
				if(field_data[0] == actual_element.getProperty('id')){
					var field_data2 = field_data[1].split('{');
					var field_data3 = field_data2[1].split('-');
					$('prop_cf_fileupload_extensions').value = field_data2[0];
					$('prop_cf_fileupload_maxsize').value = field_data3[0];
					$('prop_cf_fileupload_minsize').value = field_data3[1].replace('}', '');
				}else{
					//nothing
				}
			});
			//$('prop_'+actual_element.getProperty('type')+'_text').value = actual_element.getProperty('value');
			$('prop_cf_fileupload_done').removeEvents();
			$('prop_cf_fileupload_done').addEvent('click', saveFile.bindWithEvent(actual_element));
			
			
			////////////////////////////// CAPTCHA
		//} else if ((ParentTag == 'input')&&(element.getProperty('type') == 'file')){
		} else if (element.getFirst().hasClass('cf_captcha')){
			actual_element = $E('span', element);
			function saveCaptcha(){
				$E('label', element).setText($('prop_cf_captcha_label').value);
				//element.setProperty('value', $('prop_'+element.getProperty('type')+'_text').value);
				if($('prop_cf_captcha_description').value.trim() != ''){
					if($chk($E('div.tooltipdiv', element))){
						tooltipdiv = $E('div.tooltipdiv', element);
							
						tooltipdiv.setText($('prop_cf_captcha_label').value+' :: '+$('prop_cf_captcha_description').value.trim());
						tooltiplink = $E('a.tooltiplink', element);
						if($chk($('tool-tip-'+actual_element.getProperty('id')))){
							$('tool-tip-'+actual_element.getProperty('id')).remove();
						}
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}else{
						tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
						tooltipdiv.setText($('prop_cf_captcha_label').value+' :: '+$('prop_cf_captcha_description').value.trim());
						tooltipdiv.injectAfter(actual_element);
						tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 10px'}, 'width':"16", 'height':"16"});
						tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
						tooltipimg.injectInside(tooltiplink);
						tooltiplink.injectAfter(actual_element);
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}
				}else{
					if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
					if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
					if($chk($('tool-tip-'+actual_element.getProperty('id')))){
						$('tool-tip-'+actual_element.getProperty('id')).remove();
					}
				}
				
				
			}
			if($chk($E('div.tooltipdiv', element))){
				$('prop_cf_captcha_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
			}else{
				$('prop_cf_captcha_description').value = '';
			}
			$('prop_cf_captcha').setStyle('display','block');
			$('prop_cf_captcha_label').value = $E('label', element).getText();
			//$('prop_'+element.getProperty('type')+'_text').value = element.getProperty('value');
			$('prop_cf_captcha_done').removeEvents();
			$('prop_cf_captcha_done').addEvent('click', saveCaptcha.bindWithEvent(actual_element));
			////////////////////////////// PASSWORD BOX
		//} else if ((ParentTag == 'input')&&(element.getProperty('type') == 'password')){
		} else if (element.getFirst().hasClass('cf_password')){
			actual_element = $E('input[type=password]', element);
			function savePasswordBox(){
				$E('label', element).setText($('prop_cf_password_label').value);
				actual_element.setProperty('size', $('prop_cf_password_size').value);
				actual_element.setProperty('maxlength', $('prop_cf_password_max').value);
				$('prop_cf_password').getElements('input[name^=validation_]').each(function(checkbox){
					actual_element.removeClass(checkbox.value);
				});
				$('prop_cf_password').getElements('input[name^=validation_]').each(function(checkbox){
					if(checkbox.checked)
					actual_element.addClass(checkbox.value);
				});
				if($('prop_cf_password_description').value.trim() != ''){
					if($chk($E('div.tooltipdiv', element))){
						tooltipdiv = $E('div.tooltipdiv', element);
							
						tooltipdiv.setText($('prop_cf_password_label').value+' :: '+$('prop_cf_password_description').value.trim());
						tooltiplink = $E('a.tooltiplink', element);
						if($chk($('tool-tip-'+actual_element.getProperty('id')))){
							$('tool-tip-'+actual_element.getProperty('id')).remove();
						}
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}else{
						tooltipdiv = new Element('div', {'class':'tooltipdiv', styles:{'display':'none'}});										
						tooltipdiv.setText($('prop_cf_password_label').value+' :: '+$('prop_cf_password_description').value.trim());
						tooltipdiv.injectAfter(actual_element);
						tooltipimg = new Element('img', {'class':"tooltipimg", 'src':'components/com_chronocontact/css/images/tooltip.png', 'border':"0", 'styles':{'display':'inline', 'border':'0px solid #fff', 'margin':'0px 0px 0px 10px'}, 'width':"16", 'height':"16"});
						tooltiplink = new Element('a', {'class':"tooltiplink", 'onclick':'return false;'});//, 'title':$E('div.tooltipdiv', element).getText()});
						tooltipimg.injectInside(tooltiplink);
						tooltiplink.injectAfter(actual_element);
						var Tips1 = new Tips(tooltiplink, $E('div.tooltipdiv', element).getText(), {elementid:actual_element.getProperty('id')});
					}
				}else{
					if($chk($E('div.tooltipdiv', element)))$E('div.tooltipdiv', element).remove();
					if($chk($E('a.tooltiplink', element)))$E('a.tooltiplink', element).remove();
					if($chk($('tool-tip-'+actual_element.getProperty('id')))){
						$('tool-tip-'+actual_element.getProperty('id')).remove();
					}
				}
				
				
			}
			if($chk($E('div.tooltipdiv', element))){
				$('prop_cf_password_description').value = $E('div.tooltipdiv', element).getText().split(' :: ')[1];
			}else{
				$('prop_cf_password_description').value = '';
			}
			$('prop_cf_password').getElements('input[name^=validation_]').each(function(checkbox){
				if(actual_element.hasClass(checkbox.value))checkbox.checked = true;
				else checkbox.checked = false;
			});
			$('prop_cf_password').setStyle('display','block');
			$('prop_cf_password_label').value = $E('label', element).getText();
			$('prop_cf_password_size').value = actual_element.getProperty('size');
			$('prop_cf_password_max').value = actual_element.getProperty('maxlength');
			$('prop_cf_password_done').removeEvents();
			$('prop_cf_password_done').addEvent('click', savePasswordBox.bindWithEvent(actual_element));
			////////////////////////////// HIDDEN FIELD
		} else if (element.getFirst().hasClass('cf_hidden')){
			actual_element = $E('input[type=hidden]', element);
			function savehiddenBox(){
				actual_element.setProperty('name', $('prop_cf_hidden_name').value);
				actual_element.value = $('prop_cf_hidden_value').value;
			}
			$('prop_cf_hidden').setStyle('display','block');
			$('prop_cf_hidden_name').value = actual_element.getProperty('name');
			$('prop_cf_hidden_value').value = actual_element.getProperty('value');
			$('prop_cf_hidden_done').removeEvents();
			$('prop_cf_hidden_done').addEvent('click', savehiddenBox.bindWithEvent(actual_element));
		} else {}
		//$('temparea').setText($('cart').innerHTML);
		return this;
	} 
});
