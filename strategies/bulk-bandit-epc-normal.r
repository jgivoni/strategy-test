
# Bulk best normal bandit on epc script

library(bandit)
experiment = list()

<? foreach ($this->subtests as $subtest) : ?>
	visits <- c(<?= implode(',', $subtest['visits']) ?>)+1000
	conversions <- c(<?= implode(',', $subtest['conversions']) ?>)
	revenue <- c(<?= implode(',', $subtest['revenue']) ?>)+750
        stdev <- c(<?= implode(',', $subtest['stdev']) ?>)/2+2
	subtest <- list(visits = visits, conversions = conversions, revenue = revenue, stdev = stdev)
	experiment <- c(experiment, list(subtest))

<? endforeach; ?>

bandit <- function(visits, conversions, revenue, stdev) {
  epc <- revenue/visits
  arms <- length(visits)
    
  if (length(conversions[conversions<10]) > 0 | length(epc[epc==0|is.infinite(epc)]) > 0) {
    weights <- rep(1, arms)/arms
  } else {
    pooled_sd <- sqrt(sum(stdev^2*(visits+1))/(sum(visits)-length(visits)))
    stdevEpc <- sqrt(pooled_sd^2/(visits-1))
    #stdevEpc <- sqrt(stdev^2/(visits-1))

    ndraws <- 5000 # Number of simulations

    b <- stdevEpc^2/epc
    k <- epc/b
    weights <- as.vector(table(c(1:arms, sapply(split(rgamma(ndraws*arms, shape = k, scale = b), (0:(ndraws*arms-1) %/% arms)), function(x){return(which.max(x))})))/(ndraws+arms))
  }
  subtest$weight <- weights
  return(weights)
}

weights = sapply(experiment, function(subtest) bandit(subtest$visits, subtest$conversions, subtest$revenue, subtest$stdev)*10000)
write.table(t(weights), col.names = FALSE, row.names = FALSE)
