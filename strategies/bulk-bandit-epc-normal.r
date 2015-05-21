
# Bulk best normal bandit on epc script

library(bandit)
experiment = list()

<? foreach ($this->subtests as $subtest) : ?>
	visits <- c(<?= implode(',', $subtest['visits']) ?>)
	revenue <- c(<?= implode(',', $subtest['revenue']) ?>)
        stdev <- c(<?= implode(',', $subtest['stdev']) ?>)
	subtest <- list(visits = visits, revenue = revenue, stdev = stdev)
	experiment <- c(experiment, list(subtest))

<? endforeach; ?>

bandit <- function(visits, revenue, stdev) {
  epc <- revenue/visits
  stdevEpc <- sqrt(stdev^2/visits)

  ndraws <- 1000 # Number of simulations
  arms <- length(visits)
  
  # rnorm(ndraws*arms, subtest$revenue/subtest$trials, subtest$msd) # Random numbers under the normal distribution
  # split(, (0:(ndraws*arms-1) %/% arms)) # Split into groups of arms
  # sapply(, function(x){return(which.max(x))}) # Return the index of the best arm for each group
  # table(c(1:arms, ))/(ndraws+arms) # Pad with 1 winner for each arm, and divide by number of groups to get winner percentage
  # weights <- as.vector() # Return weights as a vector
  
  weights <- as.vector(table(c(1:arms, sapply(split(rnorm(ndraws*arms, epc, stdevEpc), (0:(ndraws*arms-1) %/% arms)), function(x){return(which.max(x))})))/(ndraws+arms))
  subtest$weight <- weights
  return(weights)
}

weights = sapply(experiment, function(subtest) bandit(subtest$visits, subtest$revenue, subtest$stdev)*10000)
write.table(t(weights), col.names = FALSE, row.names = FALSE)
