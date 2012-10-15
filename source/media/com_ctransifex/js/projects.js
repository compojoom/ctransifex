/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

var projects = new Class({
    Implements:[Options],
    options:{
        token:''
    },

    initialize:function (options) {
        var self = this;

        self.setOptions(options);

        $$('.btn[data-toggle="ctransifex"]').each(function (el) {
            el.addEvent('click', function () {
                var div = new Element('div', {
                        'class':'modal-backdrop fade in'
                    }).inject(document.body),
                    modal = self.modal = document.id('ctransifex-project-data'),
                    form = self.form = modal.getElement('.ctransifex-project-data-form');

                if (!window.sessionStorage) {
                    alert('This browser doesn\'t support sessionStorage. The extension won\'t work here...')
                } else {
                    sessionStorage.clear();
                    sessionStorage.setItem('project-id', el.get('data-id'));

                }

                modal.setStyle('display', 'block');
                var data = {
                    'project-id':sessionStorage.getItem('project-id'),
                    'token':self.options.token
                }
                modal.getElement('.btn-primary').addEvent('click', function () {
                    new Request.JSON({
                        url:form.get('action'),
                        data:data,
                        onSuccess:function (data) {
                            if (data.status == 'success') {
                                sessionStorage.setItem('resources', JSON.stringify(data.data));
                                new Element('div', {
                                    html:"We've found the following resources: " + data.data.join(', ') + " for this project "
                                }).inject(form.getElement('div'));
                                new Element('div', {
                                    html:"We'll now fetch the language stats for those resources"
                                }).inject(form.getElement('div'));
                                self.languageStats();
                            } else {
                                form.getElement('div').set('html', data.message);
                            }
                        }
                    }).send();
                });

            });
        });
    },


    languageStats:function () {
        var self = this, resources = JSON.parse(sessionStorage.getItem('resources')),
            resourcesCount = resources.length;

        resources.each(function (resource, index) {
            var data = {
                'token':self.options.token,
                'project-id':sessionStorage.getItem('project-id'),
                'resource':resource
            }
            new Request.JSON({
                url: 'index.php?option=com_ctransifex&task=transifex.languageStats&format=raw',
                data: data,
                async: false,
                onSuccess:function (data) {
                    sessionStorage.setItem(resource, JSON.stringify(data.data));
                    new Element('div', {
                        html:"We have found the following languages for the resource " + resource + ':' + data.data.join(', ')
                    }).inject(self.form.getElement('div'));

                    if (resourcesCount == (index + 1)) {
                        self.getLanguageFiles();
                    }
                }
            }).send();
        });
    },

    getLanguageFiles:function () {
        var self = this, resources = JSON.parse(sessionStorage.getItem('resources'));

        resources.each(function (resource, index) {
            console.log(resource)
            var langs = JSON.parse(sessionStorage.getItem(resource));
            langs.each(function(lang, lindex){
                var data = {
                    token: self.options.token,
                    'project-id': sessionStorage.getItem('project-id'),
                    resource: resource,
                    language: lang
                }
                new Request.JSON({
                    url: 'index.php?option=com_ctransifex&task=transifex.languageFiles&format=raw',
                    data: data,
                    async: false,
                    onComplete: function(data) {
                        new Element('div', {
                            html:lang + ' language file for resource '+ resource +' was downloaded successfully'
                        }).inject(self.form.getElement('div'));
//                        console.log();
                    }
                }).send();

//                console.log(langs);
                if(langs.length == (lindex+1)) {
                    console.log('all language files for ' + resource + ' were downloaded');
                }
            });

            if(resources.length == (index+1)) {
                self.generateLangPacks();
            }
        });
    },

    generateLangPacks: function() {
        var self = this, resources = JSON.parse(sessionStorage.getItem('resources')), allLangs = [];

        resources.each(function(resource){
            allLangs.combine(JSON.parse(sessionStorage.getItem(resource)));
        });
//        console.log(allLangs);
        allLangs.each(function(lang){
            var data = {
                token: self.options.token,
                'project-id': sessionStorage.getItem('project-id'),
                language: lang
            }

//            console.log(lang);
            new Request.JSON({
                url: 'index.php?option=com_ctransifex&task=packager.package&format=raw',
                data: data,
                async: false,
                onComplete: function(data) {
                    new Element('div', {
                        html: ' Zip package for '+ lang + 'generated'
                    }).inject(self.form.getElement('div'));
                }
            }).send();


        });
    }

});