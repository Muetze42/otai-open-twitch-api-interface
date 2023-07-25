# OTAI — Open Twitch API Interface

Test the Twitch API directly in the browser. Just authenticate, choose your endpoint and go.

* URL: [https://otai.ws](https://otai.ws)
* Ko-Fi Post: [https://ko-fi.com/post/Open-Twitch-API-Interface--Open-Beta-K3K4NF795](https://ko-fi.com/post/Open-Twitch-API-Interface--Open-Beta-K3K4NF795)

## Notice

The project is new and is in a kind of open beta.

## Information's

* The automatic Schedule update task runs hourly
* You find different Models Data Objects (like `endpoints`, `scopes`) and different data JSON formatted in the `data` directory of this repository
* You can find packages used for the website in the file's [data/packages/composer.json](/data/packages/composer.json) and [data/packages/package.json](/data/packages/package.json)
* The system time zone is UTC

### Active Endpoints

| Name                                   | Route                                        |                               Scopes                               |
|:---------------------------------------|:---------------------------------------------|:------------------------------------------------------------------:|
| Add Blocked Term                       | `/moderation/blocked_terms`                  |                  `moderator:manage:blocked_terms`                  |
| Add Channel Moderator                  | `/moderation/moderators`                     |                    `channel:manage:moderators`                     |
| Add Channel VIP                        | `/channels/vips`                             |                       `channel:manage:vips`                        |
| Assign Guest Star Slot                 | `/guest_star/slot`                           |                                                                    |
| Ban User                               | `/moderation/bans`                           |                  `moderator:manage:banned_users`                   |
| Block User                             | `/users/blocks`                              |                    `user:manage:blocked_users`                     |
| Cancel a raid                          | `/raids`                                     |                       `channel:manage:raids`                       |
| Check AutoMod Status                   | `/moderation/enforcements/status`            |                         `moderation:read`                          |
| Check User Subscription                | `/subscriptions/user`                        |                     `user:read:subscriptions`                      |
| Create Channel Stream Schedule Segment | `/schedule/segment`                          |                     `channel:manage:schedule`                      |
| Create Clip                            | `/clips`                                     |                            `clips:edit`                            |
| Create Custom Rewards                  | `/channel_points/custom_rewards`             |                    `channel:manage:redemptions`                    |
| Create EventSub Subscription           | `/eventsub/subscriptions`                    |                                                                    |
| Create Guest Star Session              | `/guest_star/session`                        |                                                                    |
| Create Poll                            | `/polls`                                     |                       `channel:manage:polls`                       |
| Create Prediction                      | `/predictions`                               |                    `channel:manage:predictions`                    |
| Create Stream Marker                   | `/streams/markers`                           |                     `channel:manage:broadcast`                     |
| Delete Channel Stream Schedule Segment | `/schedule/segment`                          |                     `channel:manage:schedule`                      |
| Delete Chat Messages                   | `/moderation/chat`                           |                  `moderator:manage:chat_messages`                  |
| Delete Custom Reward                   | `/channel_points/custom_rewards`             |                    `channel:manage:redemptions`                    |
| Delete EventSub Subscription           | `/eventsub/subscriptions`                    |                                                                    |
| Delete Guest Star Invite               | `/guest_star/invites`                        |                                                                    |
| Delete Guest Star Slot                 | `/guest_star/slot`                           |                                                                    |
| Delete Videos                          | `/videos`                                    |                      `channel:manage:videos`                       |
| End Guest Star Session                 | `/guest_star/session`                        |                                                                    |
| End Poll                               | `/polls`                                     |                       `channel:manage:polls`                       |
| End Prediction                         | `/predictions`                               |                    `channel:manage:predictions`                    |
| Get All Stream Tags                    | `/tags/streams`                              |                                                                    |
| Get AutoMod Settings                   | `/moderation/automod/settings`               |                 `moderator:read:automod_settings`                  |
| Get Banned Users                       | `/moderation/banned`                         |        `moderation:read`<br>`moderator:manage:banned_users`        |
| Get Bits Leaderboard                   | `/bits/leaderboard`                          |                            `bits:read`                             |
| Get Blocked Terms                      | `/moderation/blocked_terms`                  | `moderator:read:blocked_terms`<br>`moderator:manage:blocked_terms` |
| Get Broadcaster Subscriptions          | `/subscriptions`                             |                    `channel:read:subscriptions`                    |
| Get Channel Chat Badges                | `/chat/badges`                               |                                                                    |
| Get Channel Editors                    | `/channels/editors`                          |                       `channel:read:editors`                       |
| Get Channel Emotes                     | `/chat/emotes`                               |                                                                    |
| Get Channel Followers                  | `/channels/followers`                        |                     `moderator:read:followers`                     |
| Get Channel Guest Star Settings        | `/guest_star/channel_settings`               |                                                                    |
| Get Channel Information                | `/channels`                                  |                                                                    |
| Get Channel Stream Schedule            | `/schedule`                                  |                                                                    |
| Get Channel Teams                      | `/teams/channel`                             |                                                                    |
| Get Charity Campaign                   | `/charity/campaigns`                         |                       `channel:read:charity`                       |
| Get Charity Campaign Donations         | `/charity/donations`                         |                       `channel:read:charity`                       |
| Get Chat Settings                      | `/chat/settings`                             |                                                                    |
| Get Chatters                           | `/chat/chatters`                             |                     `moderator:read:chatters`                      |
| Get Cheermotes                         | `/bits/cheermotes`                           |                                                                    |
| Get Clips                              | `/clips`                                     |                                                                    |
| Get Content Classification Labels      | `/content_classification_labels`             |                                                                    |
| Get Creator Goals                      | `/goals`                                     |                        `channel:read:goals`                        |
| Get Custom Reward                      | `/channel_points/custom_rewards`             |     `channel:read:redemptions`<br>`channel:manage:redemptions`     |
| Get Custom Reward Redemption           | `/channel_points/custom_rewards/redemptions` |     `channel:read:redemptions`<br>`channel:manage:redemptions`     |
| Get Drops Entitlements                 | `/entitlements/drops`                        |                                                                    |
| Get Emote Sets                         | `/chat/emotes/set`                           |                                                                    |
| Get EventSub Subscriptions             | `/eventsub/subscriptions`                    |                                                                    |
| Get Extension Analytics                | `/analytics/extensions`                      |                    `analytics:read:extensions`                     |
| Get Extension Live Channels            | `/extensions/live`                           |                                                                    |
| Get Followed Channels                  | `/channels/followed`                         |                        `user:read:follows`                         |
| Get Followed Streams                   | `/streams/followed`                          |                        `user:read:follows`                         |
| Get Game Analytics                     | `/analytics/games`                           |                       `analytics:read:games`                       |
| Get Games                              | `/games`                                     |                                                                    |
| Get Global Chat Badges                 | `/chat/badges/global`                        |                                                                    |
| Get Global Emotes                      | `/chat/emotes/global`                        |                                                                    |
| Get Guest Star Invites                 | `/guest_star/invites`                        |                                                                    |
| Get Guest Star Session                 | `/guest_star/session`                        |                                                                    |
| Get Hype Train Events                  | `/hypetrain/events`                          |                     `channel:read:hype_train`                      |
| Get Moderators                         | `/moderation/moderators`                     |          `channel:manage:moderators`<br>`moderation:read`          |
| Get Polls                              | `/polls`                                     |           `channel:read:polls`<br>`channel:manage:polls`           |
| Get Predictions                        | `/predictions`                               |     `channel:read:predictions`<br>`channel:manage:predictions`     |
| Get Released Extensions                | `/extensions/released`                       |                                                                    |
| Get Shield Mode Status                 | `/moderation/shield_mode`                    |   `moderator:read:shield_mode`<br>`moderator:manage:shield_mode`   |
| Get Stream Key                         | `/streams/key`                               |                     `channel:read:stream_key`                      |
| Get Stream Markers                     | `/streams/markers`                           |        `channel:manage:broadcast`<br>`user:read:broadcast`         |
| Get Stream Tags                        | `/streams/tags`                              |                                                                    |
| Get Streams                            | `/streams`                                   |                                                                    |
| Get Teams                              | `/teams`                                     |                                                                    |
| Get Top Games                          | `/games/top`                                 |                                                                    |
| Get User Active Extensions             | `/users/extensions`                          |           `user:read:broadcast`<br>`user:edit:broadcast`           |
| Get User Block List                    | `/users/blocks`                              |                     `user:read:blocked_users`                      |
| Get User Chat Color                    | `/chat/color`                                |                                                                    |
| Get User Extensions                    | `/users/extensions/list`                     |           `user:read:broadcast`<br>`user:edit:broadcast`           |
| Get Users                              | `/users`                                     |                         `user:read:email`                          |
| Get Users Follows                      | `/users/follows`                             |                                                                    |
| Get Videos                             | `/videos`                                    |                                                                    |
| Get VIPs                               | `/channels/vips`                             |            `channel:read:vips`<br>`channel:manage:vips`            |
| Manage Held AutoMod Messages           | `/moderation/automod/message`                |                     `moderator:manage:automod`                     |
| Modify Channel Information             | `/channels`                                  |                     `channel:manage:broadcast`                     |
| Remove Blocked Term                    | `/moderation/blocked_terms`                  |                  `moderator:manage:blocked_terms`                  |
| Remove Channel Moderator               | `/moderation/moderators`                     |                    `channel:manage:moderators`                     |
| Remove Channel VIP                     | `/channels/vips`                             |                       `channel:manage:vips`                        |
| Search Categories                      | `/search/categories`                         |                                                                    |
| Search Channels                        | `/search/channels`                           |                                                                    |
| Send a Shoutout                        | `/chat/shoutouts`                            |                    `moderator:manage:shoutouts`                    |
| Send Chat Announcement                 | `/chat/announcements`                        |                  `moderator:manage:announcements`                  |
| Send Guest Star Invite                 | `/guest_star/invites`                        |                                                                    |
| Send Whisper                           | `/whispers`                                  |                       `user:manage:whispers`                       |
| Start a raid                           | `/raids`                                     |                       `channel:manage:raids`                       |
| Start Commercial                       | `/channels/commercial`                       |                     `channel:edit:commercial`                      |
| Unban User                             | `/moderation/bans`                           |                  `moderator:manage:banned_users`                   |
| Unblock User                           | `/users/blocks`                              |                    `user:manage:blocked_users`                     |
| Update AutoMod Settings                | `/moderation/automod/settings`               |                `moderator:manage:automod_settings`                 |
| Update Channel Guest Star Settings     | `/guest_star/channel_settings`               |                                                                    |
| Update Channel Stream Schedule         | `/schedule/settings`                         |                     `channel:manage:schedule`                      |
| Update Channel Stream Schedule Segment | `/schedule/segment`                          |                     `channel:manage:schedule`                      |
| Update Chat Settings                   | `/chat/settings`                             |                  `moderator:manage:chat_settings`                  |
| Update Custom Reward                   | `/channel_points/custom_rewards`             |                    `channel:manage:redemptions`                    |
| Update Drops Entitlements              | `/entitlements/drops`                        |                                                                    |
| Update Guest Star Slot                 | `/guest_star/slot`                           |                                                                    |
| Update Guest Star Slot Settings        | `/guest_star/slot_settings`                  |                                                                    |
| Update Redemption Status               | `/channel_points/custom_rewards/redemptions` |                    `channel:manage:redemptions`                    |
| Update Shield Mode Status              | `/moderation/shield_mode`                    |                   `moderator:manage:shield_mode`                   |
| Update User                            | `/users`                                     |                  `user:edit`<br>`user:read:email`                  |
| Update User Chat Color                 | `/chat/color`                                |                      `user:manage:chat_color`                      |
| Update User Extensions                 | `/users/extensions`                          |                       `user:edit:broadcast`                        |

### Inactive Endpoints

| Name                                 | Route                                |
|:-------------------------------------|:-------------------------------------|
| Create Extension Secret              | `/extensions/jwt/secrets`            |
| Get Channel iCalendar                | `/schedule/icalendar`                |
| Get Extension Bits Products          | `/bits/extensions`                   |
| Get Extension Configuration Segment  | `/extensions/configurations`         |
| Get Extension Secrets                | `/extensions/jwt/secrets`            |
| Get Extension Transactions           | `/extensions/transactions`           |
| Get Extensions                       | `/extensions`                        |
| Send Extension Chat Message          | `/extensions/chat`                   |
| Send Extension PubSub Message        | `/extensions/pubsub`                 |
| Set Extension Configuration Segment  | `/extensions/configurations`         |
| Set Extension Required Configuration | `/extensions/required_configuration` |
| Update Extension Bits Product        | `/bits/extensions`                   |

---

* 2023 Norman Huth <[https://huth.it](https://huth.it)>
* Collect with me or follow me on [LinkedIn](https://www.linkedin.com/in/normanhuth/)
