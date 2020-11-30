/* Given an array of integers nums and an integer target, return indices of the two numbers such that they add up to target.

You may assume that each input would have exactly one solution, and you may not use the same element twice.

You can return the answer in any order.

 

Example 1:

Input: nums = [2,7,11,15], target = 9
Output: [0,1]
Output: Because nums[0] + nums[1] == 9, we return [0, 1].
Example 2:

Input: nums = [3,2,4], target = 6
Output: [1,2]
Example 3:

Input: nums = [3,3], target = 6
Output: [0,1]
 

Constraints:

2 <= nums.length <= 10 power 5
-10 power 9 <= nums[i] <= 10 power 9
-10 power 9 <= target <= 10 power 9
Only one valid answer exists. */


sumTotal = function(nums, target){
    // Sanity check

    // Logic
    const res = [];
    let len = nums.length;
    for(let i=0; i< len-1; i++){
        for(let j=i+1; j<len; j++){
            if(nums[i] + num[j] == target){
                res.push(i);
                res.push(j);
            }
        }       
    }
    return res;
}

// Test Cases
const arr = [1,3,11, 8];
const target = 14;

console.log(sumTotal(arr,target));