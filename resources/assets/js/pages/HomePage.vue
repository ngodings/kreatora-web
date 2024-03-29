<template>
  <div>
    <TopNavbar />
    <div class="container">
      <div class="mt-4">
        <HomeWalletComponent :amount="walletAmount" v-if="isLoggedIn"/>
        <NotLoginComponent v-else />
        <div v-if="urlType">
          <CampaignVerticalList :title="campaignVerticalListTitle" :data="campaigns" @onClickBack="onClickBack"/>
        </div>
        <div v-else>
          <CampaignHorizontalList title="Trending" :data="campaign_home.trending" url="trending" @onClickSeeAll="onClickSeeAll" />
          <CampaignHorizontalList title="Akan Berakhir" :data="campaign_home.will_end" url="will_end" @onClickSeeAll="onClickSeeAll" />
          <CampaignHorizontalList title="Populer" :data="campaign_home.popular" url="popular" @onClickSeeAll="onClickSeeAll" />
          <CampaignHorizontalList title="Terbaru" :data="campaign_home.latest" url="latest" @onClickSeeAll="onClickSeeAll" />
        </div>
        <div class="spacer"></div>
        <BottomNavbar />
      </div>
    </div>
  </div>
</template>

<script>
import NotLoginComponent from "../components/alerts/NotLoginComponent";
import CampaignHorizontalList from "../components/homepage/CampaignHorizontalList";
import HomeWalletComponent from "../components/wallets/HomeWalletComponent";
import Apis from "../apis";
import CampaignVerticalList from "../components/homepage/CampaignVerticalList";

export default {
  name: "HomePage",
  components: {
    CampaignVerticalList,
    HomeWalletComponent,
    CampaignHorizontalList,
    NotLoginComponent
  },
  data() {
    return {
      profile: {},
      campaign_home: {},
      campaigns: []
    }
  },
  methods: {
    fetchProfile(){
      if (!this.isLoggedIn) return
      Apis.user.profile().then(({data}) => {
        this.$set(this, 'profile', data);
      }).catch((error) => {
        throw error
      })
    },
    fetchHomeCampaign(){
      Apis.campaign.home().then(({data}) => {
        this.$set(this, 'campaign_home', data.data);
      }).catch((error) => {
        throw error
      })
    },
    fetchCampaign(){
      Apis.campaign.index({
        type: this.urlType
      }).then(({data}) => {
        this.$set(this, 'campaigns', data.data);
      }).catch((error) => {
        throw error
      })
    },
    onClickSeeAll(payload) {
      this.$router.push({ name: this.isLoggedIn ? 'DashboardHomePage':'HomePage', query: { type: payload } }).then(()=>{
        this.fetchCampaign();
      })
      .catch(err => {
        if (err.name !== 'NavigationDuplicated' &&!err.message.includes('Avoided redundant navigation to current location')) {
          console.log(err);
        }
      });
    },
    onClickBack() {
      this.$router.push({ name: this.isLoggedIn ? 'DashboardHomePage':'HomePage'}).then(()=>{
        this.fetchHomeCampaign();
      })
      .catch(err => {
        if (err.name !== 'NavigationDuplicated' &&!err.message.includes('Avoided redundant navigation to current location')) {
          console.log(err);
        }
      });
    }
  },
  computed: {
    urlType() {
      return this.$route.query.type;
    },
    campaignVerticalListTitle() {
      if(this.urlType === 'trending') return "Trending";
      else if (this.urlType === 'will_end') return "Akan Berakhir";
      else if (this.urlType === 'popular') return "Populer";
      else if (this.urlType === 'latest') return "Terbaru";
      else return "";
    },
    walletAmount() {
      if (this.profile.wallet) return parseFloat(this.profile.wallet.balance);
      return 0;
    },
    isLoggedIn() {
      return this.$store.getters.isLoggedIn;
    }
  },
  mounted() {
    this.fetchProfile();
    this.urlType ? this.fetchCampaign() : this.fetchHomeCampaign();
  }
}
</script>

<style scoped>
.spacer {
  height: 50px;
}
</style>
