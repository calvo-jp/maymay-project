/**
 *
 * @param {string} email
 */
function isEmail(email) {
  const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  return email && pattern.test(email);
}
