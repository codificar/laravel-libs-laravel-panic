<template>
	<li class='nav-item'>
		<a class='nav-link dropdown-toggle text-muted waves-effect waves-dark icon-messages-admin' 
			href=''
			data-toggle='dropdown' 
			aria-haspopup='true' 
			aria-expanded='false'>
			<i class='mdi mdi-24px mdi-message-alert'></i>
			<span v-if="totalUnread" class='badge badge-danger help-panic-notification'>
				{{ totalUnreadFormated }}
			</span>
		</a>
		<div class='dropdown-menu dropdown-menu-right dropdown-panic-message animated flipInY'>
			<ul class='dropdown-user dropdown-panic-notification'>
				<div class="empty-messages" v-if="messages.length == 0">
					<p>{{emptyChat}}</p>
				</div>
				<div v-else>
					<div v-for="message in messages">
						<li class='text-left'>
							<a :href='message.link'>
								<div class='container-message'>
									<i class='ti-alert text-danger'></i> 
									<p class='text-user-name'>{{`${message.username.substring(0, 10)}...`}}</p>
									<p class='text-datetime'>{{message.datetime}}</p>	
								</div> 
								<p class='text-user-message'>{{`${message.message.substring(0, 30)}...`}}</p>
							</a>
						</li>
						<li role='separator' class='divider'></li>
					</div>
				</div>
				<li class='text-center show-all-messages'>
					<a :href='linkToAllPanic'> 
						<i class='ti-alert text-danger'></i> 
						{{showAllPanicMessages}} 
						<span v-if="totalUnread">
							- {{totalText}}: ({{ totalUnread }})
						</span>
					</a>
				</li>
			</ul>
		</div>
	</li>
</template>

<script>
import Echo from 'laravel-echo';
import axios from 'axios';

export default {
	props: [
		'echoPort',
		'echoHost',
		'linkToAllPanic',
		'urlGetPanicMessages'
	],
	data() {
		return {
			connected: false,
			emptyChat: this.trans('panic.empty_chat'),
			showAllPanicMessages: this.trans('panic.show_all_panic_messages'),
			totalText: this.trans('panic.total'),
			totalUnread: '',
			messages: []
		}
	},
	destroyed() {
		this.leaveProviderLocation();
	},
	mounted() {
	},
	created() {
		// Define o client e broadcaster
		const client = require('socket.io-client');
		const broadcaster = 'socket.io';

		const host = this.echoHost || window.location.hostname;
		const port = this.echoPort || 6001
		// Abre a conexão
		if(!window.Echo) {
			window.Echo = new Echo({
				broadcaster: broadcaster,
				client: client,
				host: `${host}:${port}`
			});
		}

		if(window.io) {
			window.io = client;
		}
		this.subscribeChatSocket();
		this.getPanicMessagesNotifications();

	},
	methods: {
		subscribeChatSocket() {
			try {
				var self = this;
				window.Echo.channel('chatPanicMessageAdminNotification')
					.listen('.newPanicMessage', e => {
						self.getPanicMessagesNotifications();
					});
			} catch(error) {
				console.log('subscribeChatSocket', error);
			}
		},

		getPanicMessagesNotifications() {
			axios.get(this.urlGetPanicMessages)
				.then(response => {
					if(response.data.success) {
						this.messages = response.data.panic_messages
						this.totalUnreadFormated =  response.data.total_unread_formated;
						this.totalUnread =  response.data.total_unread;
					}
				})
				.catch(error => {
					console.log('catch', error);
				});
		},

		/**
		 * Leave socket channel
		 * @return {void}
		 */
		leaveProviderLocation() {
			if (this.connected) {
				window.Echo.leave('chatPanicMessageAdminNotification');
			}
		},
	}
}
</script>


<style scoped>
.help-panic-notification {
    position: absolute;
    background-color: red;
    display: flex;
    justify-content: center;
    align-items: center;
    top: 6px;
    right: 0px;
    border-radius: 20px;
    font-size: 10px;font-weight: bold;
    width: 20px;
    height: 20px;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}

.dropdown-panic-message {
    position: absolute;
    right: 165px;
}

.text-left {
    text-align: left;
}

.text-center {
    text-align: center;
}

.text-user-name {
    margin-bottom: 0px;
    padding-left: 10px;
}
.text-datetime {
	margin-bottom: 0px;
    padding-right: 10px;
    font-size: 10px;
    color: #4e4e4e;
    display: flex;
    justify-content: flex-end;
    flex: 1;
}

.text-user-message {
    margin: 0px!important;
    padding-left: 30px!important;
    font-size: 13px !important;
    color: #8d8d8d!important;
}

.show-all-messages {
    font-size: 11px;
    margin: 0px;
}
.container-message {
    display: flex;
    width: 100%;
    justify-content: flex-start;
    align-items: center;
}

.dropdown-panic-notification {
    padding: 0px 5px;
    width: 100%;
}

.empty-messages {
	display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    color: #4c4c4c;
}

.icon-messages-admin {
    width: 45px;
    display: flex;
    justify-content: center;
    align-items: center;
}

@media (max-width: 767px){
    .dropdown-panic-message {
        width: 50% !important;
        margin-top: 2px !important;
        right: 159px;
    }
    .dropdown-panic-notification {
        padding: 0px 5px !important;
        width: 100% !important;
    }
}
@media (max-width: 359px){
    .dropdown-panic-message {
        width: 100% !important;
        margin-top: 0px !important;
        right: 0px;
    }
    .dropdown-panic-notification {
        padding: 0px 5px !important;
        width: 100% !important;
    }
}
</style>