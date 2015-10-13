
#Bulk best binomial bandit script

library(bandit)
experiment = list()

<? foreach ($this->subtests as $subtest) : ?>
	trials <- c(<?= implode(',', $subtest['trials']) ?>)
	successes <- c(<?= implode(',', $subtest['successes']) ?>)
	subtest <- list(trials = trials, successes = successes)
	experiment <- c(experiment, list(subtest))

<? endforeach; ?>

bbb <- function(successes, trials) {
  maxPriorTrials <- 50
  priorRate <- min(1, maxPriorTrials/sum(trials))
  priorTrials <- sum(trials) * priorRate
  priorSuccesses <- sum(successes) * priorRate
  
  alpha <- priorSuccesses + 1
  beta <- priorTrials - priorSuccesses + 1
  weights <- best_binomial_bandit_sim(successes, trials, alpha, beta)
  return(weights)
}

weights = sapply(experiment, function(subtest) bbb(subtest$successes, subtest$trials)*10000)
write.table(t(weights), col.names = FALSE, row.names = FALSE)
