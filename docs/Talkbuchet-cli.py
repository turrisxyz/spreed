#
# @copyright Copyright (c) 2022, Daniel Calviño Sánchez (danxuliu@gmail.com)
#
# @license GNU AGPL version 3 or any later version
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#

from pathlib import Path
from selenium import webdriver
from selenium.webdriver.firefox.service import Service
from urllib.request import urlopen

driver = None

def loadTalkbuchet():
	talkbuchet = Path('Talkbuchet.js').read_text()

	talkbuchet = talkbuchet + '''
	window.closeConnections = closeConnections
	window.setAudioEnabled = setAudioEnabled
	window.setVideoEnabled = setVideoEnabled
	window.setSentAudioStreamEnabled = setSentAudioStreamEnabled
	window.setSentVideoStreamEnabled = setSentVideoStreamEnabled
	window.checkPublishersConnections = checkPublishersConnections
	window.checkSubscribersConnections = checkSubscribersConnections
	window.setCredentials = setCredentials
	window.setToken = setToken
	window.setPublishersAndSubscribersCount = setPublishersAndSubscribersCount
	window.startMedia = startMedia
	window.siege = siege'''

	# Clear previous logs
	driver.get_log('browser')

	driver.set_script_timeout(120)

	driver.execute_script(talkbuchet)

def startChrome(nextcloudUrl, remoteSeleniumUrl = None):
	desiredCapabilities = webdriver.DesiredCapabilities.CHROME
	desiredCapabilities['goog:loggingPrefs'] = { 'browser':'ALL' }

	options = webdriver.ChromeOptions()
	options.add_argument("--use-fake-device-for-media-stream")
	options.add_argument("--use-fake-ui-for-media-stream")
	options.add_argument("--disable-dev-shm-usage")

	global driver
	if (remoteSeleniumUrl != None):
		driver = webdriver.Remote(
			desired_capabilities=desiredCapabilities,
			command_executor=remoteSeleniumUrl,
			options=options
		)
	else:
		driver = webdriver.Chrome(
			desired_capabilities=desiredCapabilities,
			options=options
		)

	driver.get(nextcloudUrl)

	loadTalkbuchet()

def printLogs():
	for log in driver.get_log('browser'):
		print(log['message'])

def closeConnections():
	driver.execute_script('closeConnections()')
	printLogs()

def setAudioEnabled(audioEnabled):
	driver.execute_script('setAudioEnabled(' + ('true' if audioEnabled else 'false') + ')')
	printLogs()

def setVideoEnabled(videoEnabled):
	driver.execute_script('setVideoEnabled(' + ('true' if videoEnabled else 'false') + ')')
	printLogs()

def setSentAudioStreamEnabled(sentAudioStreamEnabled):
	driver.execute_script('setSentAudioStreamEnabled(' + ('true' if sentAudioStreamEnabled else 'false') + ')')
	printLogs()

def setSentVideoStreamEnabled(sentVideoStreamEnabled):
	driver.execute_script('setSentVideoStreamEnabled(' + ('true' if sentVideoStreamEnabled else 'false') + ')')
	printLogs()

def checkPublishersConnections():
	driver.execute_script('checkPublishersConnections()')
	printLogs()

def checkSubscribersConnections():
	driver.execute_script('checkSubscribersConnections()')
	printLogs()

def setCredentials(user, appToken):
	driver.execute_script('setCredentials(\'' + user + '\', \'' + appToken + '\')')
	printLogs()

def setPublishersAndSubscribersCount(publishersCountToSet, subscribersPerPublisherCountToSet):
	driver.execute_script('setPublishersAndSubscribersCount(' + str(publishersCountToSet) + ', ' + str(subscribersPerPublisherCountToSet) + ')')
	printLogs()

def startMedia(audio, video):
	driver.execute_script('startMedia(' + ('true' if audio else 'false') + ', ' + ('true' if video else 'false') + ')')
	printLogs()

def setConnectionWarningTimeout(connectionWarningTimeout):
	driver.execute_script('setConnectionWarningTimeout(' + str(setConnectionWarningTimeout) + ')')
	printLogs()

def siege():
	driver.execute_script('await siege()')
	printLogs()
