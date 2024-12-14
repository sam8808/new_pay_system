
<template>
        <button class="tickets-toggler" @click.prevent="handleToggler">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Google_Messages_logo.svg/600px-Google_Messages_logo.svg.png?20221102143805" alt="">
        </button>
        <div class="ticket-wrapper show">
            <div v-if="!isAdmin"  class="ticket-body">
                <button @click="handleFlip" data-flip=".ticket-wrapper">Мои былеты</button>
                <div v-if="ticket instanceof Array && ticket.length" class="user-tickets">
                    <ul>
                        <li class="ticket-card user__mine-tickets" v-for="(item, index) in ticket" :key="item.id" :data-chat="item.id" @click="handleSetChaT">
                            <span class="subject-item">
                                Subject: {{item.subject}}
                            </span>
                            <span class="status">Status : {{item.status}}</span>
                        </li>
                    </ul>
                </div>
                <form action=""  @submit.prevent="handleForm" id="ticketForm">
                    <textarea name="subject" id="" placeholder="Тема" cols="30" rows="10"></textarea>
                    <label for="ticketCategory">
                        <span>Выберите категорию</span>
                    </label>
                    <select name="category" id="ticketCategory">
                        <option value="technical">Технический</option>
                        <option value="financial">Финансовый</option>
                        <option value="account">Счет</option>
                        <option value="wallet">Кошелек</option>
                        <option value="other">Другой</option>
                    </select>
                    <button class="inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-blue-600 group-hover:opacity-100 transition-opacity">Отправить</button>
                </form>
            </div>
            <div v-else class="admin-tickets">
                <div class="tickets-face">
                    <ul class="waiting-ticket-list ticket-card">
                        <li class="ticket-card" v-for="(item,index) in ticket" :key="index">
                            <span>{{item.user.email}}</span>
                            <span> {{item.category}}</span>
                            <span> {{item.subject}}</span>
                        </li>
                    </ul>
                </div>
                <div class="tickets-back">
                    <ul class="mine-ticket-list ticket-card">
                        <li class="ticket-card" v-for="(item,index) in ticket" :key="index">
                            <span>{{item.user.email}}</span>
                            <span> {{item.category}}</span>
                            <span> {{item.subject}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="chat-wrapper">
            <div class="chat-head">
                <button class="back-btn" @click="handleRemoveChat">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="256" height="256" viewBox="0 0 256 256" xml:space="preserve">
                    <defs>
                    </defs>
                                            <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                        <path d="M 65.75 90 c 0.896 0 1.792 -0.342 2.475 -1.025 c 1.367 -1.366 1.367 -3.583 0 -4.949 L 29.2 45 L 68.225 5.975 c 1.367 -1.367 1.367 -3.583 0 -4.95 c -1.367 -1.366 -3.583 -1.366 -4.95 0 l -41.5 41.5 c -1.367 1.366 -1.367 3.583 0 4.949 l 41.5 41.5 C 63.958 89.658 64.854 90 65.75 90 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                    </g>
                    </svg>
                </button>

                <div class="admin-wrapper">
                    <img src="https://www.w3schools.com/howto/img_avatar.png" alt="">
                    <div>
                        <span>Admin name:</span>
                        <span>Vlad</span>
                    </div>
                </div>
            </div>
            <div class="chat-body">
                <ul>
                    <li class="message-item" v-for="(message,index) in this.messages" :key="index">
                        {{message.message}}
                    </li>
                </ul>
            </div>
            <div class="chat-footer">

                    <textarea name="" @keydown.enter="handleEnter"
                              @keydown.ctrl.enter="insertLineBreak" v-model="message"
                              placeholder="message..." id=""  rows="1"></textarea>
                    <button>Send</button>
            </div>
        </div>

</template>

<style scoped>
    .ticket-wrapper{
        --padding : 25px;
        perspective: 1000px;
        transition: .3s all linear;
        display: grid;
        grid-template-rows: 0;
        &.backface{
            .ticket-body{
                transform: rotateY(180deg);
            }
        }
        &.show{
            grid-template-rows: 1fr;
            .ticket-body{
                opacity: 1;
                pointer-events: all;
                width: auto;
                padding: var(--padding);
            }

        }
        .ticket-card{
            &.user__mine-tickets{
                display: grid;
                gap: unset;
                width: 100%;
                border: 2px solid black ;
                margin-bottom: 12px;
                cursor: pointer;

            }
        }
        .ticket-body{
            transition: .3s all linear;
            opacity: 0;
            min-height: 0;
            pointer-events: none;
            background-color: white;
            transform-style: preserve-3d;
        }
        form{
            display: flex;
            row-gap: 12px;
            flex-direction: column;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            input, textarea, select{
                border-radius: 8px;
            }
            button{
                color: white;
                padding: 12px 6px;
                border-radius: 8px;
            }
        }
        .user-tickets{
            position: absolute;
            inset: var(--padding);
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform: rotateY(180deg);
        }

    }
    .tickets-toggler{
        width: 50px;
        height: auto;
        border-radius: 50%;
        cursor: pointer;
    }
    .ticket-card{
        display: grid;
        gap: 25px;
        li{
            display: flex;
            flex-wrap: wrap;
            text-align: center;
            span{
                flex: 1 1 100%;
            }
        }
    }
    .chat-wrapper{
        position: absolute;
        inset: 0;
        background-color: white;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        padding: 12px;
        opacity: 0;
        pointer-events: none;

        &.show{
            opacity: 1;
            pointer-events: all;
        }
        .chat-body{
            flex: 1;
        }
        .chat-head{
            display: flex;
            justify-content: center;
            border-bottom: 1px solid black;
            position: relative;
        }
        .chat-footer{

        }
        .admin-wrapper{
            img{
                margin: 12px auto 12px;
                max-width: 50px;
                max-height: 50px;
                border-radius: 50%;

            }
        }
        .back-btn{
            position: absolute;
            left: 0;
            top: 0;
            svg{
                max-width: 15px;
                max-height: 25px;
            }
        }
    }
</style>
<script>
    export default {
        name: "Tickets",
        props : {
            isAdmin: {
                type: Boolean,
                default: false
            },
            ticket: {
                type: Array,
                default: []
            }
        },
       data(){
            return {
                "message" : '',
                "messages" : [],
                'chatId' : null,
            }
       },
        methods : {
            async handleForm(e){
                const data = new FormData(e.target);
                const json = {};
                data.forEach((value, key)=>{
                    json[key] = value;
                })
                console.log(`${window.location.host}/api/tickets/create`)
                let result = await fetch(`http://${window.location.host}/api/tickets/create`, {
                    method: 'POST',
                    headers: {
                        'Content-type' : 'application/json',
                    },
                    body : JSON.stringify(json)
                })
            },
            handleToggler(){
                this.$el.parentElement.querySelector('.ticket-wrapper').classList.toggle('show')
            },
            handleFlip(){
                this.$el.parentElement.querySelector('.ticket-wrapper').classList.toggle('backface')
            },
            handleEnter(event) {
                if (!event.ctrlKey) {
                    event.preventDefault();
                    this.sendMessage();
                }
            },
            insertLineBreak(event) {
                const textarea = event.target;
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;

                this.message =
                    this.message.substring(0, start) + "\n" + this.message.substring(end);

                this.$nextTick(() => {
                    textarea.selectionStart = textarea.selectionEnd = start + 1;
                });
            },
            handleSetChaT(e){
                let element = e.target.closest('li.ticket-card') || (e.target.classList.contains('ticket-card') ? e.target : null)
                if(element){
                    let chatId = element.getAttribute('data-chat');
                    if(chatId){
                        this.setChat(chatId);
                    }
                }

            },
            handleRemoveChat(e){
                let chat = e.target.closest('.chat-wrapper')
                this.chatId = null;
                chat.classList.remove('show')
            },
            async sendMessage() {
                if (this.message.trim() !== "") {
                    let response = await fetch(`${window.location.origin}/api/tickets/messages/create`, {
                        method: "POST",
                        body : JSON.stringify({
                            'message' : this.message,
                            'ticket_id' : this.chatId,
                        }),
                        headers : {
                            "Content-type" : 'application/json'
                        }

                    })
                    let result = await response.json();
                    this.messages.push(result.messageObject);
                    this.message = "";
                }
            },
            async getChatMessages(id) {
                let response = await fetch(`${window.location.origin}/api/tickets/${id}`);
                const result = await response.json();

                return result;
            },
            async setChat(id){
                let chatWrapper = document.querySelector('.ticket-sidebar .chat-wrapper');
                let data = await fetch(`${window.location.origin}/api/tickets/${id}`);
                this.messages = await data.json()
                chatWrapper.classList.add('show');
                this.chatId = id;
            },

        },

        async mounted() {

        }
    }

</script>
