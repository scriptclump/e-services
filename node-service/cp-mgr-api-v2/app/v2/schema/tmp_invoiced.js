/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('tmp_invoiced', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'tmp_invoiced'
  });
};
