
-- A test client: testclient/abcdef
INSERT INTO oauth_clients
(client_id, client_secret, redirect_uri)
VALUES
("testclient", "$2y$10$Qq1CsKsY1eHLewwC.EZYM.x71bxJOXibz1dXetEEBrawQu90VVLV6", null);

-- A test user: rob/123456
INSERT INTO oauth_users
(username, password, first_name, last_name)
VALUES
("rob", "$2y$10$mzP0fRcTvjLE8xnhzzIhY.s4VLfr0FdkME0RX8/corX08MjFm8BHu", "Rob", "Allen");
