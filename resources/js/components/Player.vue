<template>
    <div >
        <h1>{{activePlayer.teamImage == 'allblacks.png'? 'ALLBLACK RUGBY' : 'NBA'}}</h1>
        <div :style="{display : 'flex'}">
            <div class="card" :style="{width : '1080px', height : '800px', borderColor: activePlayer.color}">
                <img :src="'/images/teams/' + activePlayer.teamImage" alt="All blacks logo" class="logo" />
                <div class="name">
                    <em>#{{activePlayer.number}}</em>
                    <h2>{{activePlayer.first_name}} <strong>{{activePlayer.last_name}}</strong></h2>
                </div>
                <div class="profile">
                    <img :src="'/images/players/' + activePlayer.image" alt="Rendell Pogi" class="headshot" />
                    <div class="features">
                        <div class="feature" v-for="(feature, key) of activePlayer.featured" :key="key">
                            <h3>{{feature.label}}</h3>
                            {{feature.value}}
                        </div>
                    </div>
                </div>
                <div class="bio">
                    <div class="data">
                        <strong>Position</strong>
                        {{activePlayer.position}}
                    </div>
                    <div class="data">
                        <strong>Weight</strong>
                        {{activePlayer.weight}}
                    </div>
                    <div class="data">
                        <strong>Height</strong>
                        {{activePlayer.height}}
                    </div>
                    <div class="data" v-if="activePlayer.age">
                        <strong>Age</strong>
                        {{activePlayer.age}}
                    </div>
                </div> 
            </div>
            <div class="vertical-text sidenav-container" :style="{ position: 'sticky', marginTop: '250px', marginLeft: '-10px'}">
                <div class="sidenav-item" :style="{background: activePlayer.color, color:'aliceblue', cursor: 'pointer'}" @click="handlePrev(activePlayer.pagination.prev.index)">
                    <a id="button-link">
                        {{activePlayer.pagination.prev.name}}
                    </a>
                </div>
                <div class="sidenav-item">
                        {{activePlayer.name}}
                </div>
                <div class="sidenav-item" :style="{background: activePlayer.color, color:'aliceblue', cursor: 'pointer'}" @click="handlePrev(activePlayer.pagination.next.index)">
                        <a id="button-link">
                        {{activePlayer.pagination.next.name}}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['data', 'active'],
        data(){
            return {
                activePlayer : {
                    pagination : {
                        prev : {index : 0},
                        next : {index : 0},
                    }
                },
                players : [],
                activeIndex : 0,
            }
        },
        async mounted() {
            console.log({
                data: this.data,
                active: this.active,
            })
                this.players = this.data
                if(this.active !== 'empty'){
                    this.activePlayer = this.active;
                }else{
                    this.activePlayer = this.players[0];
                }

                this.assemblePagination();
        },
        methods: {
            assemblePagination(){
                var pagination = [];
              
                var startIndex = 0;
                var lastIndex = this.players.length - 1;

                //get active index
                this.players.find((player, index) => {
                    if(player.id == this.activePlayer.id){
                        this.activeIndex = index; 
                    }
                })

                // process next button
                if(lastIndex == this.activeIndex){
                    var nextIndex = startIndex
                    var nexButton = {
                        index : nextIndex,
                        name : this.players[nextIndex].name
                    }
                }else{
                    var nextIndex = this.activeIndex + 1
                    var nexButton = {
                        index : nextIndex,
                        name : this.players[nextIndex].name
                    }
                }

                //prcess prev button
                if(startIndex == this.activeIndex){
                    var prevIndex =  lastIndex; //start at first 
                    var prevButton =  {
                        index : prevIndex,
                        name : this.players[prevIndex].name
                    }
                }else{
                    var prevIndex =  this.activeIndex - 1;
                    var prevButton =  {
                        index : prevIndex,
                        name : this.players[prevIndex].name
                    }
                }

                pagination = {
                    next : nexButton,
                    prev : prevButton,
                }

                this.activePlayer.pagination = pagination;

                // console.log({
                //     pagination: pagination,
                //     activePlayer: this.activePlayer,
                // })
            },
            handlePrev(index){
                this.activePlayer = this.players[index];
                this.assemblePagination();
            },
            handleNext(index){
                this.activePlayer = this.players[index];
                this.assemblePagination();
            }
        }


    }
</script>
