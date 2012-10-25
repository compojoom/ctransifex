/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.10.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

self.addEventListener('message', function(e){
//    alert(e);

    self.postMessage(e.resources);
});