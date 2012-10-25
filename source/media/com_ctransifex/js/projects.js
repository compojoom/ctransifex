/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

Request.requestQueue = Request.requestQueue || [];
Request.extend({
    callQueue:function () {
        console.log(this.requestQueue.length);
        this.requestQueue.length && this.requestQueue[0]();
    },
    addRequest:function (instance) {
        var self = this,
            temp;
        instance.addEvent('complete:once', function () {
            this.removeEvent('complete', temp);
            self.requestQueue.erase(temp);
            self.callQueue();
        });

        this.requestQueue.push(temp = function () {
            instance.send();
        });
    }
});

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
                        onComplete:function (data) {
                            if (data.status == 'success') {
                                sessionStorage.setItem('resources', JSON.stringify(data.data));
                                new Element('div', {
                                    html:"We've found the following resources: " + data.data.join(', ') + " for this project "
                                }).inject(form.getElement('div'));
                                new Element('div', {
                                    html:"We'll now fetch the language stats for those resources"
                                }).inject(form.getElement('div'));

                                setTimeout(function () {
                                    self.languageStats();
                                }, 200);

                            } else {
                                form.getElement('div').set('html', 'Something went wrong. Transifex replied with: ' + data.message);
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
            Request.addRequest(
                new Request.JSON({
                    url:'index.php?option=com_ctransifex&task=transifex.languageStats&format=raw',
                    data:data,
                    link:'chain',
                    onComplete:function (data) {
                        sessionStorage.setItem(resource, JSON.stringify(data.data));
                        new Element('div', {
                            html:"We have found the following languages for the resource " + resource + ':' + data.data.join(', ')
                        }).inject(self.form.getElement('div'));

                        if (resourcesCount == (index + 1)) {
                            self.getLanguageFiles();
                        }
                    }
                })
            )
        });

        Request.callQueue();
    },

    getLanguageFiles:function () {
        var self = this, resources = JSON.parse(sessionStorage.getItem('resources')), availableLangs = [];

        resources.each(function (resource, index) {
            availableLangs.combine(JSON.parse(sessionStorage.getItem(resource)));
        });

        var langsCount = availableLangs.length;
        (availableLangs.sort()).each(function (language, index) {
            var data = {
                token:self.options.token,
                'project-id':sessionStorage.getItem('project-id'),
                language:language
            }
            Request.addRequest(
                new Request.JSON({
                    url:'index.php?option=com_ctransifex&task=transifex.langpack&format=raw',
                    data:data,
                    link:'chain',
                    onComplete:function (data) {
                        if (data && data.status == 'success') {
                            new Element('div', {
                                html:data.message
                            }).inject(self.form.getElement('div'));
                        }

                        if (langsCount == (index + 1)) {
                            new Element('div', {
                                html:'We are ready. You can now refresh the page'
                            }).inject(self.form.getElement('div'));
                        }
                    }
                }));

        });

        Request.callQueue();
        console.log(availableLangs);
    }

});