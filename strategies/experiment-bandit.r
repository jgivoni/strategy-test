library(bandit)
trials <- c(100,101,102)
successes <- c(10,11,12)
subtest <- list(trials = trials, successes = successes)
#experiment <- list(subtest, subtest, subtest)
experiment <- rep(list(subtest), 5)
weights = sapply(experiment, function(subtest) best_binomial_bandit_sim(subtest$successes, subtest$trials))
write.table(t(weights), col.names = FALSE, row.names = FALSE)
#5000: 28sec

library(bandit)
trials <- c(100,101,102)
successes <- c(10,11,12)
subtest <- list(trials = trials, successes = successes)
for (i in 1:5) {
	weights = best_binomial_bandit_sim(subtest$successes, subtest$trials)
	write.table(t(weights), col.names = FALSE, row.names = FALSE)
}
#5000: 38sec

library(bandit)
trials <- c(100,101,102)
successes <- c(10,11,12)
subtest <- list(trials = trials, successes = successes)
experiment <- list(subtest)

trials <- c(100,101,102)
successes <- c(10,11,12)
subtest <- list(trials = trials, successes = successes)

experiment <- c(experiment, list(subtest))

weights = sapply(experiment, function(subtest) best_binomial_bandit_sim(subtest$successes, subtest$trials))
write.table(t(weights), col.names = FALSE, row.names = FALSE)
