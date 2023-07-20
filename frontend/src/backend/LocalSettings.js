import jwtDecode from 'jwt-decode';
import router from '@/router';
import { contractAbi, contractAddress, contractAddressGanache } from './SmartContract';

export default {
  TOKEN_KEY: "evoting_system_admin",
  BASE_URL_PHP: 'https://evoting.andrii.ro/api-php/public/index.php',
  IMAGES_FOLDER_PATH: 'https://evoting.andrii.ro/api-php/public/uploads',
  NODEJS_API_BLINDSIGNATURE: 'https://api-blindsignature.onrender.com',
  CONTRACT_ADDRESS: contractAddressGanache, 
  CONTRACT_ABI: contractAbi,
  INFURA_API: 'https://goerli.infura.io/v3/cfce638ae2c34acc9e8b1711f216f69b',

  logout() {
    localStorage.removeItem(this.TOKEN_KEY);
    //function to delete from database
    router.push("/admin");
  },

  getCurrentUser() {
    const token = localStorage.getItem(this.TOKEN_KEY);
    if (!token) {
      return null;
    }
    const payload = jwtDecode(token);
    return payload;
  },

  getToken() {
    return localStorage.getItem(this.TOKEN_KEY);
  },

  isLoggedIn() {
    const currentUser = this.getCurrentUser();
    return currentUser !== null;
  },

  verifyLoginAndRedirect() {
    if (!this.isLoggedIn()) {
      router.push("/admin");
    }
  },
};
