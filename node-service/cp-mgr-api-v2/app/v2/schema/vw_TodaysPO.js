/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_TodaysPO', {
    po_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    kvi: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    qty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    price: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    esp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    row_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    NewElp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ELPdifference: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ELP%: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    cp_enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'vw_TodaysPO'
  });
};
