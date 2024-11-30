class Solution {
public:
    int shoppingOffers(vector<int>& price, vector<vector<int>>& special, vector<int>& needs) {
        size_t n = price.size();
        string zero(n, '\0');
        dp[zero] = 0;
        string s;
        for(int num: needs){
            s.push_back(num);
        }
        return shoppingOffers(price, special, s);
    }

    int shoppingOffers(vector<int> &price, vector<vector<int>> &special, string &needs){
        if(!dp.count(needs)){
            int min_price = maxPrice(price, needs);
            for(auto &offer: special){
                string s(needs.size(), '\0');
                for(int i = 0; i < needs.size(); ++i){
                    if(offer[i] > needs[i]){
                        s.clear();
                        break;
                    }
                    else
                        s[i] = needs[i] - offer[i];
                }
                if(s.size()){
                    int total = shoppingOffers(price, special, s) + offer.back();
                    if(min_price > total)
                        min_price = total;
                }
            }
            dp[needs] = min_price;
        }
        return dp[needs];
    }
    
    int maxPrice(vector<int> &price, string &needs){
        int total = 0;
        for(int i = 0; i < price.size(); ++i){
            total += price[i] * needs[i];
        }
        return total;
    }

    // dp[needs]: the minimum price to buy items in `needs`.
    unordered_map<string, int> dp;
};