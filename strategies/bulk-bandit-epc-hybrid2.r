
# Bulk best hybrid (beta+gaussian) bandit on epc script
# Hybrid 2 - binom * victor's inverse gamma variance for gaussian

library(bandit)
suppressMessages(
    library(MCMCpack)
)

experiment = list()

<? foreach ($this->subtests as $subtest) : ?>
	visits <- c(<?= implode(',', $subtest['visits']) ?>)
	conversions <- c(<?= implode(',', $subtest['conversions']) ?>)
	revenue <- c(<?= implode(',', $subtest['revenue']) ?>)
        sumSqRev <- c(<?= implode(',', $subtest['sumSqRev']) ?>)
	subtest <- list(visits = visits, conversions = conversions, revenue = revenue, sumSqRev = sumSqRev)
	experiment <- c(experiment, list(subtest))

<? endforeach; ?>

# Make each row in x more like y if they are small samples
# Returns z which is x plus a proportion of y
blend <- function(x, y, sampleSizeColumn, keepSampleSize = FALSE) {
  if (keepSampleSize) {
    yr = x[[sampleSizeColumn]] / y[[sampleSizeColumn]][1]
  } else {
    yr = 1
  }
  r <- pmax(1 - x[[sampleSizeColumn]] / y[[sampleSizeColumn]][1], 0) # The ratio of primed observations to use (0-1)
  z <- list()
  for (column in names(x)) {
    z[[column]] <- x[[column]] + y[[column]] * r
    if (yr > 1) {
      # Truncate excess samples if we're just calculating a prior
      z[[column]] <- z[[column]] / yr
    }
  }
  return(z)
}

# Primes a subtest with expected metrics to account for insufficient data
# Starts out with a global default
# Uses subtest overalls
primeSubtest <- function(subtest) {
  sampleSize <- 50 # Needed number of visits per arm
  cr <- 0.03 # Expected conversion ratio, visits to signups
  epc <- 0.75 # Expected EPC, mean revenue per visit
  sumSqRev <- 750
  
  globalPrior <- subtest
  globalPrior$visits <- sampleSize
  globalPrior$conversions <- sampleSize * cr
  globalPrior$revenue <- sampleSize * epc
  globalPrior$sumSqRev <- sampleSize * sumSqRev
  subtestPrior <- lapply(subtest, sum)
  prior <- blend(subtestPrior, globalPrior, 'visits', TRUE) # Prime subtest prior with global prior if needed - this will be the prior to use
  subtest <- blend(subtest, prior, 'visits') # Prime subtest with subtest prior if needed
  return(subtest)
}

rvictor <- 
  function (ndraws, n, sum.revenues, sum.squared.revenues, mu0=1, nu0=1, alpha0=1, beta0=1) {
    mu1<-(nu0*mu0+sum.revenues)/(nu0+n)
    nu1<-nu0+n
    alpha1<-alpha0+n/2
    beta1<-beta0+(sum.squared.revenues-((sum.revenues)**2)/n)/2+nu0*((sum.revenues-n*mu0)**2)/(2*(nu0+n))
    beta1[n==0]<-beta0
    sigma2<-rinvgamma(ndraws,alpha1,beta1)
    ans <- rnorm(ndraws, mu1,sqrt(sigma2/nu1))
    return(ans)
  }

bandit <- function(visits, conversions, revenue, sumSqRev) {
    revPerConv <- revenue/conversions
    sdRpc <- sqrt(abs(sumSqRev-revPerConv^2*conversions)/(conversions-1)^2) # standard deviation of revenue per signup

    arms <- length(visits)
    ndraws <- 2000 # Number of simulations

    alpha <- conversions + 1
    beta <- visits - conversions + 1
    weights <- as.vector(table(c(1:arms, sapply(split(
        rbeta(ndraws*arms, alpha, beta) *
        rvictor(ndraws*arms, conversions, revenue, sumSqRev)
        , (0:(ndraws*arms-1) %/% arms)), function(x){return(which.max(x))})))/(ndraws+arms))

  subtest$weight <- weights
  return(weights)
}

experiment <- lapply(experiment, function(subtest) primeSubtest(subtest))
weights = sapply(experiment, function(subtest) bandit(subtest$visits, subtest$conversions, subtest$revenue, subtest$sumSqRev)*10000)
write.table(t(weights), col.names = FALSE, row.names = FALSE)
