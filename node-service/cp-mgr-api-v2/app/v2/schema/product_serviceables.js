/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('product_serviceables', {
    prod_serv_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      references: {
        model: 'products',
        key: 'product_id'
      }
    },
    seller_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    state_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false
    },
    cities: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    is_serviceable: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'product_serviceables'
  });
};
