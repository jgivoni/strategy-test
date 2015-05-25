
#Bulk best binomial bandit script

library(bandit)
experiment = list()

<? foreach ($this->subtests as $subtest) : ?>
	trials <- c(<?= implode(',', $subtest['trials']) ?>)
	successes <- c(<?= implode(',', $subtest['successes']) ?>)
	subtest <- list(trials = trials, successes = successes)
	experiment <- c(experiment, list(subtest))

<? endforeach; ?>

bbb <- function(successes, trials, alpha = 1, beta = 1) {
  weights <- best_binomial_bandit_sim(successes, trials, alpha, beta)
  return(weights)
}

weights = sapply(experiment, function(subtest) bbb(subtest$successes, subtest$trials, alpha = <?= $this->alpha ?>, beta = <?= $this->beta ?>)*10000)
write.table(t(weights), col.names = FALSE, row.names = FALSE)
